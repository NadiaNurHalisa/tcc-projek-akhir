import jwt from "jsonwebtoken";
import Booking from "../models/booking.js";

// Verify JWT token
export const verifyToken = (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith("Bearer ")) {
    return res.status(401).json({ message: "No token provided" });
  }

  const token = authHeader.split(" ")[1];

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || "JWTLOGIN");
    req.user = decoded;
    next();
  } catch (error) {
    return res.status(403).json({ message: "Failed to authenticate token" });
  }
};

// Authorize based on role
export const authorizeRole = (roles) => {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).json({ message: "User not authenticated" });
    }

    if (!roles.includes(req.user.role)) {
      return res
        .status(403)
        .json({ message: "Access denied. Insufficient permissions" });
    }

    next();
  };
};

// Middleware to check if user owns the booking
export const verifyBookingOwnership = async (req, res, next) => {
  try {
    const bookingId = req.params.id;
    const userId = req.user.id;

    // Check if booking exists and belongs to the user
    const booking = await Booking.findByPk(bookingId);

    if (!booking) {
      return res.status(404).json({ message: "Booking not found" });
    }

    // Allow admin to access any booking, or user to access their own booking
    if (req.user.role === "admin" || booking.user_id === userId) {
      next();
    } else {
      return res.status(403).json({
        message: "Access denied. You can only access your own bookings",
      });
    }
  } catch (error) {
    return res.status(500).json({
      message: "Error verifying booking ownership",
      error: error.message,
    });
  }
};

// Middleware to check if user can only access their own user data
export const verifyUserAccess = (req, res, next) => {
  try {
    const requestedUserId = parseInt(req.params.userId);
    const currentUserId = req.user.id; // Menggunakan 'id' sesuai dengan JWT token

    // Allow admin to access any user data, or user to access their own data
    if (req.user.role === "admin" || requestedUserId === currentUserId) {
      next();
    } else {
      return res
        .status(403)
        .json({ message: "Access denied. You can only access your own data" });
    }
  } catch (error) {
    return res
      .status(500)
      .json({ message: "Error verifying user access", error: error.message });
  }
};
