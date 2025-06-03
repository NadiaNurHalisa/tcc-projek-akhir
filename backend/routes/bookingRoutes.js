import express from "express";
import {
  createBooking,
  getAllBookings,
  getBookingById,
  updateBooking,
  deleteBooking,
  getBookingsByUserId,
  getBookingsByRoomId,
  getBookingsByDateRange,
} from "../controllers/bookingController.js";
import {
  authorizeRole,
  verifyBookingOwnership,
  verifyToken,
} from "../middleware/authMiddleware.js";

const router = express.Router();
router.post("/", verifyToken, createBooking);
router.get("/user/:userId", verifyToken, getBookingsByUserId);
router.get("/:id", verifyToken, verifyBookingOwnership, getBookingById);

router.put("/:id", verifyToken, authorizeRole(["admin"]), updateBooking);
router.get("/", verifyToken, authorizeRole(["admin"]), getAllBookings);
router.delete("/:id", verifyToken, authorizeRole(["admin"]), deleteBooking);
router.get(
  "/room/:roomId",
  verifyToken,
  authorizeRole(["admin"]),
  getBookingsByRoomId
);
router.get(
  "/date-range",
  verifyToken,
  authorizeRole(["admin"]),
  getBookingsByDateRange
);

export default router;
