#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <thread>
#include <chrono>
#include "client.h"

int main(int argc, char* argv[]) {
   if (argc < 4) {
       std::cerr << "Usage: " << argv[0] << " <local_ip> <dest_ip> <dest_port> <file_path> [prefix] [max_retry_resends] [client_ack_timer_interval] [keep_alive_timer_interval] [send_timeout] [handshake_timeout] [connect_timeout] [reconnect_attempts] [backoff_time] [log_metrics] [log_raw_packets]" << std::endl;
       return 1;
   }

   std::string local_ip_str = argv[1];
   std::string dest_ip_str = argv[2];
   std::string dest_port_str = argv[3];
   std::string file_path = argv[4];

   std::string prefix = (argc > 5) ? argv[5] : "STU_CLIENT";
   std::string max_retry_resends = (argc > 6) ? argv[6] : "5";
   std::string client_ack_timer_interval_str = (argc > 7) ? argv[7] : "1000";
   std::string keep_alive_timer_interval_str = (argc > 8) ? argv[8] : "2000";
   std::string send_timeout_str = (argc > 9) ? argv[9] : "5000";
   std::string handshake_timeout_str = (argc > 10) ? argv[10] : "5000";
   std::string connect_timeout_str = (argc > 11) ? argv[11] : "5000";
   std::string reconnect_attempts_str = (argc > 12) ? argv[12] : "3";
   std::string backoff_time_str = (argc > 13) ? argv[13] : "2000";
   std::string log_metrics_str = (argc > 14) ? argv[14] : "false";
   std::string log_raw_packets_str = (argc > 15) ? argv[15] : "false";

   bool false_log_metrics = (log_metrics_str == "true");
   bool false_log_raw_packets = (log_raw_packets_str == "true");

   bool client_running = true;

   Ltp::Config config;
   // define standard variables
   config.log_prefix = prefix;
   config.host_name = "STU";
   config.local_ip.change_to(local_ip_str);
   config.dest_ip.change_to(dest_ip_str);
   config.dest_port = std::stoi(dest_port_str);
   config.sim_latency = 0;
   config.sim_jitter = 0;
   config.n_threads = 1;
   config.log_metrics = false_log_metrics;
   config.max_resend_retry_attempts = std::stoi(max_retry_resends);
   config.client_ack_timer_interval = std::stoi(client_ack_timer_interval_str);
   config.keep_alive_timer_interval = std::stoi(keep_alive_timer_interval_str);
   config.rx_buffer_size = 1000;
   config.max_packet_size = 65000;
   config.send_timeout = std::stoi(send_timeout_str);
   config.handshake_timeout = std::stoi(handshake_timeout_str);
   config.connect_timeout = std::stoi(connect_timeout_str);
   config.ping_interval = 0;
   config.sim_duplication_ratio = 0;
   config.sim_loss_ratio = 0;
   config.log_raw_packets = false_log_raw_packets;
   config.reconnect_attempts = std::stoi(reconnect_attempts_str);
   config.backoff_time = std::stoi(backoff_time_str);
   config.handshake_timer_interval = 250;
   config.max_payload_data_size = config.max_packet_size - 100;
   config.sim_bandwidth_limit = 0;
   config.app_header_size = 0;
   config.keep_alive_as_ping = false;
   config.connect_attempts = 100;
   config.server_timer_interval = 100;

   // start client instance
   std::cout << "[STU-Client] Initializing client..." << std::endl;
   auto client = std::make_unique<Ltp::Client>(config);
   client->set_on_connect_callback([&]() {
       std::cout << "[STU-Client] Connected to server successfully!" << std::endl;
   });
   client->set_on_disconnect_callback([&]() {
       std::cout << "[STU-Client] Disconnected from server!" << std::endl;
   });
   client->set_on_message_callback([&](const char* data, size_t len) {
       std::cout << "[STU-Client] Received msg of size: " << len << " bytes" << std::endl;
       std::string response(data, len);
       if (response == "ACK_FILE") {
           std::cout << "[STU-Client] File acknowledged by server. Exiting." << std::endl;
           client->disconnect();
           client_running = false;
       }
   });

   std::cout << "[STU-Client] Connecting to " << dest_ip_str << ":" << dest_port_str << "..." << std::endl;
   client->connect();

   // wait for connection
   int wait_count = 0;
   while (!client->is_connected() && client_running && wait_count < 100) {
       std::this_thread::sleep_for(std::chrono::milliseconds(100));
       wait_count++;
   }

   if (!client->is_connected()) {
       std::cerr << "[STU-Client] Connection failed or timed out." << std::endl;
       return 1;
   }

   // read file
   std::cout << "[STU-Client] Opening file: " << file_path << std::endl;
   std::ifstream infile(file_path, std::ios::binary);
   if (!infile.is_open()) {
       std::cerr << "[STU-Client] Failed to open file: " << file_path << std::endl;
       return 1;
   }

   std::vector<char> buffer((std::istreambuf_iterator<char>(infile)), std::istreambuf_iterator<char>());
   infile.close();

   std::cout << "[STU-Client] File size: " << buffer.size() << " bytes. Sending..." << std::endl;
   
   // send file contents
   client->send(buffer.data(), buffer.size());
   std::cout << "[STU-Client] File sent. Waiting for ACK_FILE from server..." << std::endl;

   // wait for ack or disconnect
   while (client_running && client->is_connected()) {
       std::this_thread::sleep_for(std::chrono::milliseconds(100));
   }

   std::cout << "[STU-Client] Client thread ending." << std::endl;
   return 0;
}
