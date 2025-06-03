# Hotel Booking System API Documentation

---

## Overview

This API documentation provides comprehensive information about the hotel booking system's endpoints for authentication, user management, room management, and booking operations. The API uses JWT (JSON Web Token) for authentication and implements role-based access control with "user" and "admin" roles.

## Base URL
```
http://localhost:3000
```

## Authentication

All protected endpoints require JWT authentication using the Authorization header:
```
Authorization: Bearer <token>
```

JWT tokens are obtained through the login endpoints and remain valid for a limited time. Different endpoints require different permission levels:
- **Public endpoints**: Can be accessed without authentication
- **User endpoints**: Require user authentication
- **Admin endpoints**: Require admin authentication
- **Owner-specific endpoints**: Require the authenticated user to be the owner of the resource

## Status Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **500**: Internal Server Error

## Pagination

Some endpoints returning collections (like rooms or bookings) support pagination with query parameters:
- `page`: Page number (starting from 0)
- `limit`: Number of items per page

---

# Authentication Endpoints

## 1. Register a New User

**Endpoint:** `POST /auth/register`

**Headers:**
- Content-Type: application/json

**Request Body:**
```json
{
  "username": "testuser",
  "password": "password123",
  "email": "test@example.com"
}
```

**Response Examples:**
- **Success (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "role": "user"
  }
}
```
- **Error (400):**
```json
{
  "message": "Username or email already exists"
}
```

---

## 2. User Login

**Endpoint:** `POST /auth/login`

**Headers:**
- Content-Type: application/json

**Request Body:**
```json
{
  "username": "testuser",
  "password": "password123"
}
```

**Response Example:**
```json
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "role": "user"
  }
}
```

---

## 3. Register a New Admin

**Endpoint:** `POST /auth/register-admin`

**Headers:**
- Content-Type: application/json

**Request Body:**
```json
{
  "username": "adminuser",
  "password": "admin123",
  "email": "admin@example.com",
  "no_hp": "1234567890"
}
```

**Response Examples:**
- **Success (201):**
```json
{
  "message": "Admin registered successfully",
  "user": {
    "id": 4,
    "username": "adminuser",
    "email": "admin@example.com",
    "role": "admin",
    "no_hp": "1234567890"
  }
}
```
- **Error (400):**
```json
{
  "message": "Username or email already exists"
}
```

---

## 4. Admin Login

**Endpoint:** `POST /auth/login-admin`

**Headers:**
- Content-Type: application/json

**Request Body:**
```json
{
  "username": "adminuser",
  "password": "admin123"
}
```

**Response Example:**
```json
{
  "message": "Admin login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 4,
    "username": "adminuser",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

---

## 5. Logout (if implemented)

**Endpoint:** `POST /auth/logout`

**Headers:**
- Authorization: Bearer <JWT_TOKEN>

**Note:** This endpoint may not be fully implemented. Expected to invalidate the current JWT token.

---

# User Management Endpoints

## 6. Create User (Admin only)

**Endpoint:** `POST /users`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Request Body:**
```json
{
  "username": "newuser",
  "password": "password",
  "email": "newuser@example.com",
  "no_hp": "1234567890",
  "role": "user"
}
```

**Response Example:**
```json
{
  "message": "User created successfully",
  "user": {
    "id": 5,
    "username": "newuser",
    "email": "newuser@example.com",
    "role": "user"
  }
}
```

---

## 7. Get All Users (Admin only)

**Endpoint:** `GET /users`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
[
  {
    "id": 1,
    "username": "updateduser",
    "email": "updated@example.com",
    "no_hp": null,
    "role": "user",
    "created_at": "2025-06-03T09:37:11.000Z",
    "updated_at": "2025-06-03T14:37:15.000Z"
  },
  {
    "id": 4,
    "username": "adminuser",
    "email": "admin@example.com",
    "no_hp": "1234567890",
    "role": "admin",
    "created_at": "2025-06-03T09:50:13.000Z",
    "updated_at": "2025-06-03T09:50:13.000Z"
  }
]
```

---

## 8. Get Single User (Admin only)

**Endpoint:** `GET /users/{id}`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
{
  "id": 1,
  "username": "updateduser",
  "email": "updated@example.com",
  "no_hp": null,
  "role": "user",
  "created_at": "2025-06-03T09:37:11.000Z",
  "updated_at": "2025-06-03T14:37:15.000Z"
}
```

---

## 9. Update User (Admin only)

**Endpoint:** `PUT /users/{id}`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Request Body:**
```json
{
  "username": "updateduser",
  "email": "updated@example.com"
}
```

**Response Example:**
```json
{
  "message": "User updated successfully"
}
```

---

## 10. Delete User (Admin only)

**Endpoint:** `DELETE /users/{id}`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
{
  "message": "User deleted successfully"
}
```

---

## 11. Get User Profile

**Endpoint:** `GET /users/profile`

**Headers:**
- Authorization: Bearer <JWT_TOKEN>

**Response Example:**
```json
{
  "id": 1,
  "username": "testuser",
  "email": "test@example.com",
  "role": "user",
  "created_at": "2025-06-03T09:37:11.000Z",
  "updated_at": "2025-06-03T14:37:15.000Z"
}
```

---

## 12. Update User Profile

**Endpoint:** `PUT /users/profile`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <JWT_TOKEN>

**Request Body:**
```json
{
  "username": "updateduser",
  "email": "updated@example.com",
  "fullName": "Updated User Name",
  "phone": "1234567890"
}
```

**Response Example:**
```json
{
  "message": "Profile updated successfully"
}
```

---

## 13. Change Password

**Endpoint:** `PUT /users/change-password`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <JWT_TOKEN>

**Request Body:**
```json
{
  "currentPassword": "oldpassword",
  "newPassword": "newpassword123"
}
```

**Response Example:**
```json
{
  "message": "Password changed successfully"
}
```

---

# Room Management Endpoints

## 14. Get All Rooms

**Endpoint:** `GET /rooms`

**Headers:**
- Authorization: Bearer <JWT_TOKEN> (optional for public access)

**Response Example:**
```json
{
  "result": [
    {
      "id": 1,
      "name": "Deluxe Room",
      "description": "A luxurious room with a view.",
      "price": "1000000.00",
      "facilities": "['AC', 'WiFi', 'TV', 'Bathroom']",
      "status": "available",
      "image_url": "https://example.com/deluxe.jpg",
      "created_at": "2025-06-03T09:40:02.000Z",
      "updated_at": "2025-06-03T09:40:02.000Z"
    }
  ],
  "page": 0,
  "limit": 10,
  "totalPages": 1,
  "totalRows": 1
}
```

---

## 15. Get Single Room

**Endpoint:** `GET /rooms/{id}`

**Headers:**
- Authorization: Bearer <JWT_TOKEN> (optional for public access)

**Response Example:**
```json
{
  "id": 1,
  "name": "Deluxe Room",
  "description": "A luxurious room with a view.",
  "price": "1000000.00",
  "facilities": "['AC', 'WiFi', 'TV', 'Bathroom']",
  "status": "available",
  "image_url": "https://example.com/deluxe.jpg",
  "created_at": "2025-06-03T09:40:02.000Z",
  "updated_at": "2025-06-03T09:40:02.000Z"
}
```

---

## 16. Create Room (Admin only)

**Endpoint:** `POST /rooms`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Request Body:**
```json
{
  "name": "Superior Room",
  "description": "A comfortable room.",
  "price": 750000,
  "facilities": "['AC', 'WiFi']",
  "status": "available",
  "image_url": "https://example.com/superior.jpg"
}
```

**Response Example:**
```json
{
  "message": "Room created successfully",
  "room": {
    "id": 2,
    "name": "Superior Room",
    "description": "A comfortable room.",
    "price": 750000,
    "facilities": "['AC', 'WiFi']",
    "status": "available",
    "image_url": "https://example.com/superior.jpg",
    "created_at": "2025-06-03T14:18:10.000Z",
    "updated_at": "2025-06-03T14:18:10.000Z"
  }
}
```

---

## 17. Update Room (Admin only)

**Endpoint:** `PUT /rooms/{id}`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Request Body:**
```json
{
  "price": 800000,
  "status": "occupied"
}
```

**Response Example:**
```json
{
  "message": "Room updated successfully",
  "room": {
    "id": 1,
    "name": "Deluxe Room",
    "description": "A luxurious room with a view.",
    "price": 800000,
    "facilities": "['AC', 'WiFi', 'TV', 'Bathroom']",
    "status": "occupied",
    "image_url": "https://example.com/deluxe.jpg",
    "created_at": "2025-06-03T09:40:02.000Z",
    "updated_at": "2025-06-03T14:36:20.000Z"
  }
}
```

---

## 18. Delete Room (Admin only)

**Endpoint:** `DELETE /rooms/{id}`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
{
  "message": "Room deleted successfully"
}
```

---

## 19. Get Available Rooms

**Endpoint:** `GET /rooms/available`

**Headers:**
- Authorization: Bearer <JWT_TOKEN>

**Response Example:**
```json
{
  "result": [
    {
      "id": 1,
      "name": "Deluxe Room",
      "description": "A luxurious room with a view.",
      "price": "1000000.00",
      "facilities": "['AC', 'WiFi', 'TV', 'Bathroom']",
      "status": "available",
      "image_url": "https://example.com/deluxe.jpg"
    }
  ]
}
```

---

# Booking Management Endpoints

## 20. Create Booking

**Endpoint:** `POST /bookings`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <JWT_TOKEN>

**Request Body:**
```json
{
  "user_id": 1,
  "room_id": 1,
  "start_date": "2024-01-15",
  "end_date": "2024-01-20",
  "status": "pending",
  "total_price": 4000000,
  "notes": "Late arrival"
}
```

**Response Example:**
```json
{
  "message": "Booking created successfully",
  "booking": {
    "id": 1,
    "user_id": 1,
    "room_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-01-20",
    "status": "pending",
    "total_price": 4000000,
    "notes": "Late arrival",
    "created_at": "2025-06-03T13:52:19.000Z",
    "updated_at": "2025-06-03T13:52:19.000Z"
  }
}
```

---

## 21. Get All Bookings (Admin only)

**Endpoint:** `GET /bookings`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "room_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-01-20",
    "status": "pending",
    "total_price": "4000000.00",
    "notes": "Late arrival",
    "created_at": "2025-06-03T13:52:19.000Z",
    "updated_at": "2025-06-03T13:52:19.000Z",
    "user": {
      "id": 1,
      "username": "updateduser",
      "email": "updated@example.com",
      "role": "user"
    },
    "room": {
      "id": 1,
      "name": "Deluxe Room",
      "description": "A luxurious room with a view.",
      "price": "1000000.00",
      "status": "available"
    }
  }
]
```

---

## 22. Get Single Booking

**Endpoint:** `GET /bookings/{id}`

**Headers:**
- Authorization: Bearer <JWT_TOKEN>

**Note:** Requires booking ownership or admin role.

**Response Example:**
```json
{
  "id": 1,
  "user_id": 1,
  "room_id": 1,
  "start_date": "2024-01-15",
  "end_date": "2024-01-20",
  "status": "pending",
  "total_price": "4000000.00",
  "notes": "Late arrival",
  "created_at": "2025-06-03T13:52:19.000Z",
  "updated_at": "2025-06-03T13:52:19.000Z"
}
```

---

## 23. Update Booking

**Endpoint:** `PUT /bookings/{id}`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <JWT_TOKEN>

**Note:** Requires booking ownership or admin role.

**Request Body:**
```json
{
  "status": "confirmed"
}
```

**Response Example:**
```json
{
  "message": "Booking updated successfully"
}
```

---

## 24. Delete Booking (Admin only)

**Endpoint:** `DELETE /bookings/{id}`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
{
  "msg": "Booking deleted"
}
```

---

## 25. Get Bookings by User ID

**Endpoint:** `GET /bookings/user/{user_id}`

**Headers:**
- Authorization: Bearer <JWT_TOKEN>

**Note:** Users can only access their own bookings; admins can access any user's bookings.

**Response Example:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "room_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-01-20",
    "status": "pending",
    "total_price": "4000000.00",
    "notes": "Late arrival",
    "created_at": "2025-06-03T13:52:19.000Z",
    "updated_at": "2025-06-03T13:52:19.000Z"
  }
]
```

---

## 26. Get Bookings by Room ID (Admin only)

**Endpoint:** `GET /bookings/room/{room_id}`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Response Example:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "room_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-01-20",
    "status": "pending",
    "total_price": "4000000.00",
    "notes": "Late arrival",
    "created_at": "2025-06-03T13:52:19.000Z",
    "updated_at": "2025-06-03T13:52:19.000Z",
    "user": {
      "id": 1,
      "username": "updateduser",
      "email": "updated@example.com"
    }
  }
]
```

---

## 27. Get Bookings by Date Range (Admin only)

**Endpoint:** `GET /bookings/date-range`

**Headers:**
- Authorization: Bearer <ADMIN_JWT_TOKEN>

**Query Parameters:**
- `startDate`: Start date in YYYY-MM-DD format
- `endDate`: End date in YYYY-MM-DD format

**Example:** `/bookings/date-range?startDate=2024-01-01&endDate=2024-01-31`

**Response Example:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "room_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-01-20",
    "status": "pending",
    "total_price": "4000000.00",
    "notes": "Late arrival"
  }
]
```

---

# Error Responses

## Common Error Formats

**Authentication Error (401):**
```json
{
  "message": "Unauthorized: Invalid or missing token"
}
```

**Permission Error (403):**
```json
{
  "message": "Access denied. Insufficient permissions"
}
```

**Not Found Error (404):**
```json
{
  "message": "Resource not found"
}
```

**Validation Error (400):**
```json
{
  "message": "Validation error",
  "errors": [
    "Username is required",
    "Email format is invalid"
  ]
}
```

**Server Error (500):**
```json
{
  "message": "Internal server error"
}
```

---

# Notes

1. **JWT Token Expiration**: Tokens have a limited lifespan. When a token expires, you'll receive a 401 Unauthorized response.

2. **Role-Based Access**: Some endpoints require admin privileges. Regular users attempting to access admin-only endpoints will receive a 403 Forbidden response.

3. **Data Validation**: All endpoints validate input data. Invalid data will result in a 400 Bad Request response with details about validation errors.

4. **Rate Limiting**: The API may implement rate limiting to prevent abuse. Excessive requests may result in temporary blocking.

5. **CORS**: The API supports Cross-Origin Resource Sharing (CORS) for web applications.

6. **Content-Type**: All POST and PUT requests must include `Content-Type: application/json` header.
