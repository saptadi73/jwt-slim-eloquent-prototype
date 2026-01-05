# Dashboard API Documentation

## Overview
Dokumentasi lengkap untuk API Dashboard Service AC Lestari yang menyediakan data untuk visualisasi metrics dan charts.

---

## Table of Contents
1. [Overview Data Structure](#overview-data-structure)
2. [Endpoints](#endpoints)
   - [Complete Dashboard](#complete-dashboard)
   - [Dashboard Summary](#dashboard-summary)
   - [Workorder Metrics](#workorder-metrics)
   - [Attendance Metrics](#attendance-metrics)
   - [Workorder Status](#workorder-status)
   - [Sales Data](#sales-data)
   - [Purchases Data](#purchases-data)
   - [Expenses Data](#expenses-data)
   - [Comparison Data](#comparison-data)
3. [Frontend Integration](#frontend-integration)
4. [Chart Examples](#chart-examples)

---

## Overview Data Structure

### Workorder Metrics
Menampilkan statistik workorder untuk KPI cards.

```json
{
  "today": 120,      // Workorder dibuat hari ini
  "pending": 40,     // Workorder belum selesai
  "finished": 80,    // Workorder sudah selesai
  "total": 120       // Total semua workorder
}
```

### Attendance Metrics
Menampilkan statistik kehadiran pegawai.

```json
{
  "present": 45,     // Pegawai hadir hari ini
  "total": 50,       // Total pegawai
  "absent": 5        // Pegawai tidak hadir
}
```

### Chart Data Structure
Data untuk line chart, bar chart, dan comparison chart.

```json
[
  {
    "month": "Jan",        // Label bulan singkat
    "monthFull": "January", // Nama bulan penuh
    "sales": 150000,       // Nilai penjualan
    "purchases": 80000,    // Nilai pembelian
    "expenses": 20000      // Nilai biaya
  }
]
```

---

## Endpoints

### Complete Dashboard
Mengambil semua data dashboard (metrics + charts).

**Endpoint:**
```
GET /dashboard?months=6
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Query Parameters:**
- `months` (integer, optional): Jumlah bulan yang diambil (default: 6)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "workorder_metrics": {
      "today": 120,
      "pending": 40,
      "finished": 80,
      "total": 120
    },
    "attendance_metrics": {
      "present": 45,
      "total": 50,
      "absent": 5
    },
    "workorder_status": {
      "completed": 80,
      "pending": 40,
      "total": 120
    },
    "sales_data": [
      {
        "month": "Jan",
        "monthFull": "January",
        "sales": 150000
      },
      {
        "month": "Feb",
        "monthFull": "February",
        "sales": 175000
      }
    ],
    "purchases_data": [
      {
        "month": "Jan",
        "monthFull": "January",
        "purchases": 80000
      },
      {
        "month": "Feb",
        "monthFull": "February",
        "purchases": 95000
      }
    ],
    "expenses_data": [
      {
        "month": "Jan",
        "monthFull": "January",
        "expenses": 20000
      },
      {
        "month": "Feb",
        "monthFull": "February",
        "expenses": 25000
      }
    ],
    "comparison_data": [
      {
        "month": "Jan",
        "monthFull": "January",
        "sales": 150000,
        "purchases": 80000,
        "expenses": 20000
      }
    ]
  },
  "message": "Dashboard data retrieved"
}
```

**Use Case:**
- Load semua data dashboard sekaligus saat halaman dashboard dibuka
- Optimasi: Jika ingin hanya metrics saja, gunakan endpoint `/dashboard/summary`

---

### Dashboard Summary
Mengambil hanya metrics (tanpa chart data) untuk loading lebih cepat.

**Endpoint:**
```
GET /dashboard/summary
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "workorder_metrics": {
      "today": 120,
      "pending": 40,
      "finished": 80,
      "total": 120
    },
    "attendance_metrics": {
      "present": 45,
      "total": 50,
      "absent": 5
    },
    "workorder_status": {
      "completed": 80,
      "pending": 40,
      "total": 120
    }
  },
  "message": "Dashboard summary retrieved"
}
```

**Use Case:**
- Load KPI cards terlebih dahulu untuk UX lebih baik
- Load chart data secara terpisah dengan AJAX request

---

### Workorder Metrics
Mengambil metrics workorder saja.

**Endpoint:**
```
GET /dashboard/workorder-metrics
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "today": 120,
    "pending": 40,
    "finished": 80,
    "total": 120
  },
  "message": "Workorder metrics retrieved"
}
```

---

### Attendance Metrics
Mengambil metrics kehadiran pegawai.

**Endpoint:**
```
GET /dashboard/attendance-metrics
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "present": 45,
    "total": 50,
    "absent": 5
  },
  "message": "Attendance metrics retrieved"
}
```

---

### Workorder Status
Mengambil status workorder untuk pie chart.

**Endpoint:**
```
GET /dashboard/workorder-status
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "completed": 80,
    "pending": 40,
    "total": 120
  },
  "message": "Workorder status retrieved"
}
```

**Chart Usage:**
```javascript
const response = await fetch('/dashboard/workorder-status', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const result = await response.json();

// Untuk Chart.js Pie Chart
const ctx = document.getElementById('workorderChart').getContext('2d');
new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ['Completed', 'Pending'],
    datasets: [{
      data: [result.data.completed, result.data.pending],
      backgroundColor: ['#3b82f6', '#ef4444']
    }]
  }
});
```

---

### Sales Data
Mengambil data penjualan per bulan.

**Endpoint:**
```
GET /dashboard/sales?months=6
```

**Query Parameters:**
- `months` (integer, optional): Jumlah bulan (default: 6)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "month": "Jan",
      "monthFull": "January",
      "sales": 150000
    },
    {
      "month": "Feb",
      "monthFull": "February",
      "sales": 175000
    },
    {
      "month": "Mar",
      "monthFull": "March",
      "sales": 165000
    },
    {
      "month": "Apr",
      "monthFull": "April",
      "sales": 190000
    },
    {
      "month": "May",
      "monthFull": "May",
      "sales": 210000
    },
    {
      "month": "Jun",
      "monthFull": "June",
      "sales": 225000
    }
  ],
  "message": "Sales data retrieved"
}
```

**Chart Usage (Chart.js):**
```javascript
const response = await fetch('/dashboard/sales?months=6', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const result = await response.json();

const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: result.data.map(d => d.month),
    datasets: [{
      label: 'Sales',
      data: result.data.map(d => d.sales),
      borderColor: '#10b981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: true }
    }
  }
});
```

---

### Purchases Data
Mengambil data pembelian per bulan.

**Endpoint:**
```
GET /dashboard/purchases?months=6
```

**Query Parameters:**
- `months` (integer, optional): Jumlah bulan (default: 6)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "month": "Jan",
      "monthFull": "January",
      "purchases": 80000
    },
    {
      "month": "Feb",
      "monthFull": "February",
      "purchases": 95000
    },
    {
      "month": "Mar",
      "monthFull": "March",
      "purchases": 85000
    },
    {
      "month": "Apr",
      "monthFull": "April",
      "purchases": 100000
    },
    {
      "month": "May",
      "monthFull": "May",
      "purchases": 110000
    },
    {
      "month": "Jun",
      "monthFull": "June",
      "purchases": 120000
    }
  ],
  "message": "Purchases data retrieved"
}
```

---

### Expenses Data
Mengambil data biaya/expense per bulan.

**Endpoint:**
```
GET /dashboard/expenses?months=6
```

**Query Parameters:**
- `months` (integer, optional): Jumlah bulan (default: 6)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "month": "Jan",
      "monthFull": "January",
      "expenses": 20000
    },
    {
      "month": "Feb",
      "monthFull": "February",
      "expenses": 25000
    },
    {
      "month": "Mar",
      "monthFull": "March",
      "expenses": 22000
    },
    {
      "month": "Apr",
      "monthFull": "April",
      "expenses": 28000
    },
    {
      "month": "May",
      "monthFull": "May",
      "expenses": 30000
    },
    {
      "month": "Jun",
      "monthFull": "June",
      "expenses": 32000
    }
  ],
  "message": "Expenses data retrieved"
}
```

---

### Comparison Data
Mengambil data perbandingan Purchases, Expenses vs Sales.

**Endpoint:**
```
GET /dashboard/comparison?months=6
```

**Query Parameters:**
- `months` (integer, optional): Jumlah bulan (default: 6)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "month": "Jan",
      "monthFull": "January",
      "sales": 150000,
      "purchases": 80000,
      "expenses": 20000
    },
    {
      "month": "Feb",
      "monthFull": "February",
      "sales": 175000,
      "purchases": 95000,
      "expenses": 25000
    },
    {
      "month": "Mar",
      "monthFull": "March",
      "sales": 165000,
      "purchases": 85000,
      "expenses": 22000
    },
    {
      "month": "Apr",
      "monthFull": "April",
      "sales": 190000,
      "purchases": 100000,
      "expenses": 28000
    },
    {
      "month": "May",
      "monthFull": "May",
      "sales": 210000,
      "purchases": 110000,
      "expenses": 30000
    },
    {
      "month": "Jun",
      "monthFull": "June",
      "sales": 225000,
      "purchases": 120000,
      "expenses": 32000
    }
  ],
  "message": "Comparison data retrieved"
}
```

**Chart Usage (Mixed Bar-Line Chart):**
```javascript
const response = await fetch('/dashboard/comparison?months=6', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const result = await response.json();

const ctx = document.getElementById('comparisonChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: result.data.map(d => d.month),
    datasets: [
      {
        label: 'Purchases',
        type: 'bar',
        data: result.data.map(d => d.purchases),
        backgroundColor: '#f59e0b'
      },
      {
        label: 'Expenses',
        type: 'bar',
        data: result.data.map(d => d.expenses),
        backgroundColor: '#ef4444'
      },
      {
        label: 'Sales',
        type: 'line',
        data: result.data.map(d => d.sales),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: true } }
  }
});
```

---

## Frontend Integration

### React Example
```jsx
import { useEffect, useState } from 'react';
import { Line, Pie, Bar } from 'react-chartjs-2';

export function Dashboard() {
  const [dashboard, setDashboard] = useState(null);
  const token = localStorage.getItem('token');

  useEffect(() => {
    // Load complete dashboard
    fetch('/dashboard?months=6', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
      .then(res => res.json())
      .then(result => setDashboard(result.data));
  }, [token]);

  if (!dashboard) return <div>Loading...</div>;

  return (
    <div className="dashboard">
      {/* KPI Cards */}
      <div className="metrics-grid">
        <MetricCard 
          title="Work Orders Today"
          value={dashboard.workorder_metrics.today}
          color="blue"
        />
        <MetricCard 
          title="Pending Work Orders"
          value={dashboard.workorder_metrics.pending}
          color="red"
        />
        <MetricCard 
          title="Finished Work Orders"
          value={dashboard.workorder_metrics.finished}
          color="green"
        />
        <MetricCard 
          title="Employees Present"
          value={`${dashboard.attendance_metrics.present}/${dashboard.attendance_metrics.total}`}
          color="purple"
        />
      </div>

      {/* Charts */}
      <div className="charts-grid">
        <div className="chart-container">
          <h3>Workorder Status</h3>
          <Pie data={{
            labels: ['Completed', 'Pending'],
            datasets: [{
              data: [
                dashboard.workorder_status.completed,
                dashboard.workorder_status.pending
              ],
              backgroundColor: ['#3b82f6', '#ef4444']
            }]
          }} />
        </div>

        <div className="chart-container">
          <h3>Sales (Monthly)</h3>
          <Line data={{
            labels: dashboard.sales_data.map(d => d.month),
            datasets: [{
              label: 'Sales',
              data: dashboard.sales_data.map(d => d.sales),
              borderColor: '#10b981'
            }]
          }} />
        </div>

        <div className="chart-container">
          <h3>Purchases (Monthly)</h3>
          <Line data={{
            labels: dashboard.purchases_data.map(d => d.month),
            datasets: [{
              label: 'Purchases',
              data: dashboard.purchases_data.map(d => d.purchases),
              borderColor: '#f59e0b'
            }]
          }} />
        </div>

        <div className="chart-container">
          <h3>Purchase, Expenses vs Sales</h3>
          <Bar data={{
            labels: dashboard.comparison_data.map(d => d.month),
            datasets: [
              {
                label: 'Purchases',
                data: dashboard.comparison_data.map(d => d.purchases),
                backgroundColor: '#f59e0b'
              },
              {
                label: 'Expenses',
                data: dashboard.comparison_data.map(d => d.expenses),
                backgroundColor: '#ef4444'
              },
              {
                label: 'Sales',
                data: dashboard.comparison_data.map(d => d.sales),
                backgroundColor: '#3b82f6'
              }
            ]
          }} />
        </div>
      </div>
    </div>
  );
}
```

### Vue Example
```vue
<template>
  <div class="dashboard" v-if="dashboard">
    <!-- KPI Cards -->
    <div class="metrics-grid">
      <MetricCard 
        title="Work Orders Today"
        :value="dashboard.workorder_metrics.today"
        color="blue"
      />
      <MetricCard 
        title="Pending Work Orders"
        :value="dashboard.workorder_metrics.pending"
        color="red"
      />
      <MetricCard 
        title="Finished Work Orders"
        :value="dashboard.workorder_metrics.finished"
        color="green"
      />
      <MetricCard 
        title="Employees Present"
        :value="`${dashboard.attendance_metrics.present}/${dashboard.attendance_metrics.total}`"
        color="purple"
      />
    </div>

    <!-- Charts -->
    <div class="charts-grid">
      <div class="chart-container">
        <h3>Workorder Status</h3>
        <PieChart :data="workorderStatusData" />
      </div>
      <div class="chart-container">
        <h3>Sales (Monthly)</h3>
        <LineChart :data="salesChartData" />
      </div>
      <div class="chart-container">
        <h3>Purchases (Monthly)</h3>
        <LineChart :data="purchasesChartData" />
      </div>
      <div class="chart-container">
        <h3>Purchase, Expenses vs Sales</h3>
        <BarChart :data="comparisonChartData" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const dashboard = ref(null);
const token = localStorage.getItem('token');

onMounted(async () => {
  const response = await fetch('/dashboard?months=6', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const result = await response.json();
  dashboard.value = result.data;
});

const workorderStatusData = computed(() => ({
  labels: ['Completed', 'Pending'],
  datasets: [{
    data: [
      dashboard.value?.workorder_status.completed,
      dashboard.value?.workorder_status.pending
    ],
    backgroundColor: ['#3b82f6', '#ef4444']
  }]
}));

const salesChartData = computed(() => ({
  labels: dashboard.value?.sales_data.map(d => d.month) || [],
  datasets: [{
    label: 'Sales',
    data: dashboard.value?.sales_data.map(d => d.sales) || [],
    borderColor: '#10b981'
  }]
}));
</script>
```

---

## Chart Examples

### Using Chart.js

**Installation:**
```bash
npm install chart.js react-chartjs-2
```

**Complete Example:**
```javascript
import Chart from 'chart.js/auto';

async function initDashboard() {
  const token = localStorage.getItem('token');
  const response = await fetch('/dashboard?months=6', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const result = await response.json();
  const data = result.data;

  // Workorder Status Pie Chart
  const pieCtx = document.getElementById('workorderStatusChart').getContext('2d');
  new Chart(pieCtx, {
    type: 'doughnut',
    data: {
      labels: ['Completed', 'Pending'],
      datasets: [{
        data: [data.workorder_status.completed, data.workorder_status.pending],
        backgroundColor: ['#3b82f6', '#ef4444'],
        borderColor: '#fff',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  // Sales Line Chart
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: data.sales_data.map(d => d.month),
      datasets: [{
        label: 'Sales',
        data: data.sales_data.map(d => d.sales),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointBackgroundColor: '#10b981'
      }]
    }
  });

  // Comparison Mixed Chart
  const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
  new Chart(comparisonCtx, {
    type: 'bar',
    data: {
      labels: data.comparison_data.map(d => d.month),
      datasets: [
        {
          label: 'Purchases',
          type: 'bar',
          data: data.comparison_data.map(d => d.purchases),
          backgroundColor: '#f59e0b',
          order: 2
        },
        {
          label: 'Expenses',
          type: 'bar',
          data: data.comparison_data.map(d => d.expenses),
          backgroundColor: '#ef4444',
          order: 2
        },
        {
          label: 'Sales',
          type: 'line',
          data: data.comparison_data.map(d => d.sales),
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          order: 1,
          pointRadius: 5
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Call saat DOM ready
document.addEventListener('DOMContentLoaded', initDashboard);
```

---

## Testing with cURL

### Get Complete Dashboard
```bash
curl -X GET "http://localhost:8080/dashboard?months=6" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Summary Only
```bash
curl -X GET "http://localhost:8080/dashboard/summary" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Sales Data
```bash
curl -X GET "http://localhost:8080/dashboard/sales?months=6" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Comparison Data
```bash
curl -X GET "http://localhost:8080/dashboard/comparison?months=12" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Performance Notes

1. **Query Optimization:**
   - Gunakan endpoint `/dashboard/summary` untuk quick load metrics
   - Load charts dengan AJAX terpisah jika perlu

2. **Caching:**
   - Dashboard data bisa di-cache di client untuk beberapa menit
   - Refresh data secara berkala dengan interval tertentu

3. **Database:**
   - Pastikan index pada kolom `tanggal` di workorders, sale_orders, purchase_orders
   - Gunakan aggregation query untuk performa lebih baik

4. **Large Dataset:**
   - Batasi parameter `months` maksimal 24 bulan
   - Untuk data historis lebih lama, buat endpoint terpisah dengan pagination

---

## Last Updated
- January 5, 2026

## Version
- 1.0.0
