import { Sequelize } from "sequelize";
import db from "../config/database.js";
import User from "./user.js";
import Room from "./room.js";

const Booking = db.define(
  "bookings",
  {
    user_id: {
      type: Sequelize.INTEGER,
      allowNull: false,
      references: {
        model: User,
        key: "id",
      },
    },
    room_id: {
      type: Sequelize.INTEGER,
      allowNull: false,
      references: {
        model: Room,
        key: "id",
      },
    },
    date: {
      type: Sequelize.DATEONLY,
      allowNull: true,
    },
    start_date: {
      type: Sequelize.DATEONLY,
      allowNull: true,
    },
    end_date: {
      type: Sequelize.DATEONLY,
      allowNull: true,
    },
    status: {
      type: Sequelize.ENUM("pending", "confirmed", "cancelled", "completed"),
      defaultValue: "pending",
    },
    total_price: {
      type: Sequelize.DECIMAL(10, 2),
    },
    notes: {
      type: Sequelize.TEXT,
    },
  },
  {
    timestamps: true,
    createdAt: "created_at",
    updatedAt: "updated_at",
  }
);

// Define associations
Booking.belongsTo(User, { foreignKey: "user_id", as: "user" });
Booking.belongsTo(Room, { foreignKey: "room_id", as: "room" });
User.hasMany(Booking, { foreignKey: "user_id", as: "bookings" });
Room.hasMany(Booking, { foreignKey: "room_id", as: "bookings" });

export default Booking;
