import express from "express";
import {
  getRooms,
  getRoomById,
  createRoom,
  updateRoom,
  deleteRoom,
} from "../controllers/roomController.js";
import { authorizeRole, verifyToken } from "../middleware/authMiddleware.js";

const router = express.Router();

router.get("/", verifyToken, getRooms);
router.get("/:id", verifyToken, getRoomById);
router.post("/", verifyToken, authorizeRole(["admin"]), createRoom);
router.put("/:id", verifyToken, authorizeRole(["admin"]), updateRoom);
router.delete("/:id", verifyToken, authorizeRole(["admin"]), deleteRoom);

export default router;
