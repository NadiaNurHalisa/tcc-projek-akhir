import express from "express";
import {
  getAllUsers,
  getUserById,
  updateUser,
  deleteUser,
} from "../controllers/userController.js";
import { verifyToken, authorizeRole } from "../middleware/authMiddleware.js";

const router = express.Router();

// Get all users
router.get("/", verifyToken, authorizeRole(["admin"]), getAllUsers);

// Get user by ID
router.get("/:id", verifyToken, authorizeRole(["admin"]), getUserById);

// Update user
router.put("/:id", verifyToken, authorizeRole(["admin"]), updateUser);

// Delete user
router.delete("/:id", verifyToken, authorizeRole(["admin"]), deleteUser);

export default router;
