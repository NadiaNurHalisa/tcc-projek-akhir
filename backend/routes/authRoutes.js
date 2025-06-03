import express from "express";
import {
  register,
  login,
  registerAdmin,
  loginAdmin,
} from "../controllers/userController.js";

const router = express.Router();

// Register a new user
router.post("/register", register);

// Register a new admin
router.post("/register-admin", registerAdmin);

// Login user
router.post("/login", login);

// Login admin
router.post("/login-admin", loginAdmin);

export default router;
