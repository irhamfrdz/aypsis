import React, { useState, useEffect } from 'react';
import { 
  StyleSheet, 
  Text, 
  View, 
  FlatList, 
  TouchableOpacity, 
  Platform,
  SafeAreaView,
  ActivityIndicator
} from 'react-native';

const API_BASE = Platform.OS === 'android' ? 'http://10.0.2.2:8000/api' : 'http://localhost:8000/api';

export default function StowageDashboard() {
  const [ships, setShips] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`${API_BASE}/stowage-plans/ships`)
      .then(res => res.json())
      .then(data => {
        setShips(data);
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  const renderShip = ({ item }: { item: string }) => (
    <TouchableOpacity style={styles.shipCard}>
      <View style={styles.cardHeader}>
        <Text style={styles.shipName}>{item}</Text>
        <View style={styles.badge}>
          <Text style={styles.badgeText}>Active</Text>
        </View>
      </View>
      <Text style={styles.shipDetails}>Tap to view stowage plan</Text>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>AYPSIS</Text>
        <Text style={styles.subtitle}>Stowage Plan Management</Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.sectionTitle}>Available Ships</Text>
        {loading ? (
          <ActivityIndicator size="large" color="#00E676" style={styles.loader} />
        ) : ships.length === 0 ? (
          <Text style={styles.emptyText}>No ships with active manifests found.</Text>
        ) : (
          <FlatList
            data={ships}
            keyExtractor={item => item}
            renderItem={renderShip}
            contentContainerStyle={styles.listContainer}
            showsVerticalScrollIndicator={false}
          />
        )}
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0F172A', // Sleek dark mode background
  },
  header: {
    paddingHorizontal: 24,
    paddingTop: 40,
    paddingBottom: 20,
    backgroundColor: '#1E293B',
    borderBottomWidth: 1,
    borderBottomColor: '#334155',
  },
  title: {
    fontSize: 28,
    fontWeight: '800',
    color: '#F8FAFC',
    letterSpacing: 1,
  },
  subtitle: {
    fontSize: 14,
    color: '#94A3B8',
    marginTop: 4,
    fontWeight: '500',
  },
  content: {
    flex: 1,
    padding: 24,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#E2E8F0',
    marginBottom: 16,
  },
  listContainer: {
    paddingBottom: 20,
  },
  shipCard: {
    backgroundColor: '#1E293B',
    borderRadius: 16,
    padding: 20,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#334155',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 8,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  shipName: {
    fontSize: 20,
    fontWeight: '700',
    color: '#F1F5F9',
  },
  badge: {
    backgroundColor: '#059669',
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 20,
  },
  badgeText: {
    color: '#ECFDF5',
    fontSize: 12,
    fontWeight: '600',
  },
  shipDetails: {
    fontSize: 14,
    color: '#94A3B8',
  },
  loader: {
    marginTop: 40,
  },
  emptyText: {
    color: '#64748B',
    fontSize: 16,
    textAlign: 'center',
    marginTop: 40,
  }
});
