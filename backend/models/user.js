import { Sequelize } from "sequelize";
import db from "../config/database.js";

const User = db.define("users", {
    username: {
        type: Sequelize.STRING(255),
        allowNull: false,
        unique: true
    },
    password: {
        type: Sequelize.STRING(255),
        allowNull: false
    },
    email: {
        type: Sequelize.STRING(255),
        unique: true
    },
    no_hp: {
        type: Sequelize.STRING(20)
    },
    role: {
        type: Sequelize.ENUM('user', 'admin'),
        defaultValue: 'user'
    }
}, {
    timestamps: true,
    createdAt: 'created_at',
    updatedAt: 'updated_at'
});

export default User;
