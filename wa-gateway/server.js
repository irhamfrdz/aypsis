const express = require('express');
const cors = require('cors');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const pino = require('pino');
const qrcodeTerminal = require('qrcode-terminal');
const QRCode = require('qrcode');
const path = require('path');
const fs = require('fs');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 3000;
const SESSION_DIR = path.join(__dirname, 'auth_session');

let sock = null;
let qrCodeString = null;
let connectionStatus = 'connecting'; // 'connecting' | 'open' | 'close'
let connectedUser = null;

async function startWhatsApp() {
    if (!fs.existsSync(SESSION_DIR)) {
        fs.mkdirSync(SESSION_DIR, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(SESSION_DIR);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }),
        printQRInTerminal: false,
        auth: state,
        browser: ['AYPSIS Shipping System', 'Chrome', '1.0.0'],
        syncFullHistory: false
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            qrCodeString = qr;
            connectionStatus = 'connecting';
            console.log('\n📱 Scan QR Code berikut dengan WhatsApp HP Anda:');
            qrcodeTerminal.generate(qr, { small: true });
            console.log(`Atau buka di browser: http://localhost:${PORT}/qr\n`);
        }

        if (connection === 'close') {
            connectionStatus = 'close';
            connectedUser = null;
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log(`⚠️ Koneksi WhatsApp terputus (Status: ${statusCode}). Mencoba menghubungkan ulang: ${shouldReconnect}`);

            if (shouldReconnect) {
                setTimeout(startWhatsApp, 3000);
            } else {
                console.log('❌ Sesi telah logout. Silakan scan QR code baru.');
                if (fs.existsSync(SESSION_DIR)) {
                    fs.rmSync(SESSION_DIR, { recursive: true, force: true });
                }
                setTimeout(startWhatsApp, 2000);
            }
        } else if (connection === 'open') {
            connectionStatus = 'open';
            qrCodeString = null;
            connectedUser = sock.user?.id || 'Connected';
            console.log('✅ WhatsApp Gateway Terkoneksi Sukses! User:', connectedUser);
        }
    });
}

// ── ENDPOINTS ─────────────────────────────────────────────────────────────

// Cek Status Koneksi
app.get('/status', (req, res) => {
    res.json({
        status: true,
        connection: connectionStatus,
        user: connectedUser,
        isReady: connectionStatus === 'open'
    });
});

// Tampilkan QR Code di Browser (HTML)
app.get('/qr', async (req, res) => {
    if (connectionStatus === 'open') {
        return res.send(`
            <html>
                <body style="font-family:sans-serif; text-align:center; padding:50px;">
                    <h2 style="color:#16a34a;">✅ WhatsApp Sudah Terkoneksi!</h2>
                    <p>Nomor Akun: <b>${connectedUser}</b></p>
                    <p style="color:#6b7280;">Gateway siap menerima pesan broadcast dari AYPSIS.</p>
                </body>
            </html>
        `);
    }

    if (!qrCodeString) {
        return res.send(`
            <html>
                <head><meta http-equiv="refresh" content="3"></head>
                <body style="font-family:sans-serif; text-align:center; padding:50px;">
                    <h2>⏳ Menyiapkan QR Code...</h2>
                    <p>Halaman ini akan refresh otomatis dalam 3 detik.</p>
                </body>
            </html>
        `);
    }

    try {
        const qrImage = await QRCode.toDataURL(qrCodeString);
        res.send(`
            <html>
                <head><meta http-equiv="refresh" content="20"></head>
                <body style="font-family:sans-serif; text-align:center; padding:40px; background:#f8fafc;">
                    <div style="background:#fff; display:inline-block; padding:30px; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                        <h2 style="color:#1e293b; margin-top:0;">Scan QR Code WhatsApp</h2>
                        <p style="color:#64748b; font-size:14px;">Buka WhatsApp di HP &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat</p>
                        <img src="${qrImage}" style="width:280px; height:280px; margin:20px 0; border:1px solid #e2e8f0; border-radius:8px;" />
                        <p style="color:#94a3b8; font-size:12px;">Halaman akan refresh otomatis setiap 20 detik.</p>
                    </div>
                </body>
            </html>
        `);
    } catch (e) {
        res.status(500).send('Gagal membuat gambar QR: ' + e.message);
    }
});

// Endpoint JSON QR Data untuk dirender di dalam Blade View AYPSIS
app.get('/qr-data', async (req, res) => {
    if (connectionStatus === 'open') {
        return res.json({
            status: true,
            isReady: true,
            user: connectedUser,
            qrImage: null
        });
    }

    if (!qrCodeString) {
        return res.json({
            status: false,
            isReady: false,
            message: 'Menyiapkan QR Code...',
            qrImage: null
        });
    }

    try {
        const qrImage = await QRCode.toDataURL(qrCodeString);
        res.json({
            status: true,
            isReady: false,
            user: null,
            qrImage: qrImage
        });
    } catch (e) {
        res.status(500).json({ status: false, error: e.message });
    }
});

// Endpoint Logout Sesi
app.post('/logout', async (req, res) => {
    try {
        if (sock) {
            try { await sock.logout(); } catch(err) {}
        }
        if (fs.existsSync(SESSION_DIR)) {
            fs.rmSync(SESSION_DIR, { recursive: true, force: true });
        }
        connectionStatus = 'connecting';
        connectedUser = null;
        qrCodeString = null;
        setTimeout(startWhatsApp, 1500);
        res.json({ status: true, message: 'Berhasil logout. Sesi dihapus dan QR baru dibuat.' });
    } catch (e) {
        res.status(500).json({ status: false, error: e.message });
    }
});

// Helper normalisasi nomor ke format JID (628xxx@s.whatsapp.net)
function formatToJid(phone) {
    let clean = String(phone || '').replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) {
        clean = '62' + clean.substring(1);
    } else if (!clean.startsWith('62')) {
        clean = '62' + clean;
    }
    return clean + '@s.whatsapp.net';
}

// Kirim Pesan Tunggal
app.post('/send-message', async (req, res) => {
    if (connectionStatus !== 'open' || !sock) {
        return res.status(503).json({
            status: false,
            error: 'WhatsApp Gateway belum terkoneksi. Silakan scan QR code terlebih dahulu.'
        });
    }

    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: false, error: 'Phone dan message wajib diisi.' });
    }

    try {
        const jid = formatToJid(phone);
        const result = await sock.sendMessage(jid, { text: message });
        res.json({
            status: true,
            message: 'Pesan berhasil dikirim',
            data: { jid, messageId: result?.key?.id }
        });
    } catch (error) {
        console.error('Error send-message:', error);
        res.status(500).json({ status: false, error: error.message || 'Gagal mengirim pesan' });
    }
});

// Jalankan Server
app.listen(PORT, () => {
    console.log(`=========================================`);
    console.log(`🚀 WA Gateway Server running on port ${PORT}`);
    console.log(`📡 Status API: http://localhost:${PORT}/status`);
    console.log(`📱 Scan QR:   http://localhost:${PORT}/qr`);
    console.log(`=========================================`);
    startWhatsApp();
});
