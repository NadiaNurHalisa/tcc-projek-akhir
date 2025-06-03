import { Sequelize } from "sequelize";
import db from "../config/database.js";

const Room = db.define(
  "rooms",
  {
    name: {
      type: Sequelize.STRING(255),
      allowNull: false,
    },
    description: {
      type: Sequelize.TEXT,
    },
    price: {
      type: Sequelize.DECIMAL(10, 2),
      allowNull: false,
    },
    capacity: {
      type: Sequelize.INTEGER,
      defaultValue: 1,
    },
    facilities: {
      type: Sequelize.TEXT,
    },
    status: {
      type: Sequelize.ENUM("available", "occupied", "maintenance"),
      defaultValue: "available",
    },
    image_url: {
      type: Sequelize.STRING(500),
    },
  },
  {
    timestamps: true,
    createdAt: "created_at",
    updatedAt: "updated_at",
  }
);

export default Room;
