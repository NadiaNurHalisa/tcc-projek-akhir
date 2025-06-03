# Kos Booking System - Frontend Documentation

## Overview
Dokumentasi ini menjelaskan halaman-halaman yang perlu dibuat untuk frontend aplikasi booking kos berdasarkan API backend yang tersedia.

## User Roles & Access Control

### 🔹 **USER (Penyewa Kos)**
- Role: `user`
- Dapat membuat booking untuk diri sendiri
- Dapat melihat dan mengelola booking milik sendiri
- Dapat melihat daftar kamar yang tersedia

### 🔸 **ADMIN (Pengelola Kos)**
- Role: `admin`
- Dapat mengelola semua data (users, rooms, bookings)
- Dapat melihat laporan dan statistik
- Memiliki akses penuh ke sistem

---

## 📄 **Halaman yang Perlu Dibuat**

### 1. **Authentication Pages**

#### 🔐 **Login Page** (`/login`)
- **Akses**: Public
- **Fitur**:
  - Form login untuk user dan admin
  - Toggle antara user/admin login
  - Redirect berdasarkan role setelah login
- **API Endpoint**: 
  - `POST /auth/login` (untuk user)
  - `POST /auth/login-admin` (untuk admin)

#### 📝 **Register Page** (`/register`)
- **Akses**: Public
- **Fitur**:
  - Form registrasi user baru
  - Validasi email dan username unique
- **API Endpoint**: `POST /auth/register`

---

### 2. **User Dashboard Pages**

#### 🏠 **User Dashboard** (`/dashboard`)
- **Akses**: User only
- **Fitur**:
  - Overview booking aktif
  - Quick access ke fitur utama
  - Profil singkat user

#### 🏨 **Room List Page** (`/rooms`)
- **Akses**: User only
- **Fitur**:
  - Daftar kamar tersedia dengan pagination
  - Search dan filter (nama, harga, fasilitas)
  - Sorting (harga, nama)
- **API Endpoint**: `GET /rooms`

#### 🏪 **Room Detail Page** (`/rooms/:id`)
- **Akses**: User only
- **Fitur**:
  - Detail lengkap kamar
  - Gambar kamar
  - Fasilitas dan harga
  - Button untuk booking
- **API Endpoint**: `GET /rooms/:id`

#### 📅 **Create Booking Page** (`/booking/create`)
- **Akses**: User only
- **Fitur**:
  - Form pembuatan booking
  - Pilih tanggal check-in/out
  - Kalkulasi harga otomatis
- **API Endpoint**: `POST /bookings`

#### 📋 **My Bookings Page** (`/my-bookings`)
- **Akses**: User only
- **Fitur**:
  - Daftar booking milik user
  - Status booking (pending, confirmed, cancelled)
  - Detail setiap booking
- **API Endpoint**: `GET /bookings/user/:userId`

#### ✏️ **Edit Booking Page** (`/booking/edit/:id`)
- **Akses**: User only (booking milik sendiri)
- **Fitur**:
  - Form edit booking
  - Ubah tanggal atau catatan
- **API Endpoint**: 
  - `GET /bookings/:id`
  - `PUT /bookings/:id`

#### 👤 **User Profile Page** (`/profile`)
- **Akses**: User only
- **Fitur**:
  - Edit profil user
  - Ubah password
  - Riwayat booking

---

### 3. **Admin Dashboard Pages**

#### 🎛️ **Admin Dashboard** (`/admin`)
- **Akses**: Admin only
- **Fitur**:
  - Statistik overview (total users, rooms, bookings)
  - Grafik pendapatan
  - Booking terbaru
- **API Endpoints**: 
  - `GET /users`
  - `GET /rooms`
  - `GET /bookings`

#### 👥 **User Management** (`/admin/users`)
- **Akses**: Admin only
- **Fitur**:
  - Daftar semua user
  - Search dan filter user
  - Add/Edit/Delete user
- **API Endpoints**:
  - `GET /users`
  - `GET /users/:id`
  - `PUT /users/:id`
  - `DELETE /users/:id`

#### 🏨 **Room Management** (`/admin/rooms`)
- **Akses**: Admin only
- **Fitur**:
  - Daftar semua kamar
  - Status kamar (available, occupied, maintenance)
  - Add/Edit/Delete kamar
- **API Endpoints**:
  - `GET /rooms`
  - `POST /rooms`
  - `PUT /rooms/:id`
  - `DELETE /rooms/:id`

#### 📊 **Booking Management** (`/admin/bookings`)
- **Akses**: Admin only
- **Fitur**:
  - Daftar semua booking
  - Filter by status, date, user, room
  - Update status booking
  - Delete booking
- **API Endpoints**:
  - `GET /bookings`
  - `GET /bookings/room/:roomId`
  - `GET /bookings/date-range`
  - `PUT /bookings/:id`
  - `DELETE /bookings/:id`

#### 📈 **Reports Page** (`/admin/reports`)
- **Akses**: Admin only
- **Fitur**:
  - Laporan pendapatan
  - Laporan occupancy rate
  - Export data ke Excel/PDF
- **API Endpoints**:
  - `GET /bookings/date-range`
  - `GET /bookings/room/:roomId`

---

## 🔒 **Security & Navigation**

### Route Protection
```javascript
// Protected Routes
const ProtectedRoute = ({ children, requiredRole }) => {
  const { user, token } = useAuth();
  
  if (!token) {
    return <Navigate to="/login" />;
  }
  
  if (requiredRole && user.role !== requiredRole) {
    return <Navigate to="/unauthorized" />;
  }
  
  return children;
};
```

### Navigation Structure
```
Public:
├── /login
├── /register
└── /

User Dashboard:
├── /dashboard
├── /rooms
├── /rooms/:id
├── /booking/create
├── /my-bookings
├── /booking/edit/:id
└── /profile

Admin Dashboard:
├── /admin
├── /admin/users
├── /admin/rooms
├── /admin/bookings
└── /admin/reports
```

---

## 🎨 **UI/UX Recommendations**

### Design Components Needed:
1. **Navigation Bar** - dengan role-based menu
2. **Sidebar** - untuk admin dashboard
3. **Cards** - untuk room display dan booking items
4. **Tables** - untuk data management (admin)
5. **Forms** - untuk booking, registration, room management
6. **Modals** - untuk confirmations dan quick actions
7. **Charts** - untuk admin reports dan statistics

### Responsive Design:
- Mobile-first approach
- Tablet dan desktop optimization
- Touch-friendly interfaces

---

## 🔄 **API Integration Notes**

### Authentication Headers:
```javascript
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json'
}
```

### Error Handling:
- 401: Redirect to login
- 403: Show unauthorized message
- 404: Show not found page
- 500: Show error message

### Data Validation:
- Client-side validation sebelum API call
- Server response validation
- Error message display

---

## 🚀 **Deployment Considerations**

### Environment Variables:
```
REACT_APP_API_BASE_URL=http://localhost:3000
REACT_APP_JWT_SECRET=your_jwt_secret
```

### Build Process:
- Optimize untuk production
- Code splitting untuk performance
- PWA capabilities (optional)

---

## 📱 **Additional Features (Optional)**

1. **Real-time Notifications** - untuk status booking
2. **Chat System** - komunikasi user-admin
3. **Payment Integration** - untuk pembayaran booking
4. **Calendar View** - untuk availability kamar
5. **Photo Upload** - untuk room images
6. **Reviews & Ratings** - sistem rating kamar

---

**Catatan**: Semua endpoint API sudah tersedia di backend dan siap untuk diintegrasikan dengan frontend yang akan dibuat.
