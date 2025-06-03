import Room from "../models/room.js";
import { Op } from "sequelize";

export const getRooms = async (req, res) => {
  try {
    const page = parseInt(req.query.page) || 0;
    const limit = parseInt(req.query.limit) || 10;
    const search = req.query.search || "";
    const offset = limit * page;
    const sort = req.query.sort || "id"; // Default sorting by id
    const order = req.query.order || "ASC"; // Default ascending order

    const { count, rows: rooms } = await Room.findAndCountAll({
      where: {
        [Op.or]: [
          {
            name: {
              [Op.like]: "%" + search + "%",
            },
          },
          {
            description: {
              [Op.like]: "%" + search + "%",
            },
          },
        ],
      },
      offset: offset,
      limit: limit,
      order: [[sort, order]], // Apply sorting
    });

    const totalPages = Math.ceil(count / limit);

    res.json({
      result: rooms,
      page: page,
      limit: limit,
      totalPages: totalPages,
      totalRows: count,
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Internal Server Error" });
  }
};

export const getRoomById = async (req, res) => {
  try {
    const room = await Room.findByPk(req.params.id);
    if (!room) {
      return res.status(404).json({ message: "Room not found" });
    }
    res.json(room);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Internal Server Error" });
  }
};

export const createRoom = async (req, res) => {
  try {
    const {
      name,
      description,
      price,
      capacity,
      facilities,
      status,
      image_url,
    } = req.body;
    const room = await Room.create({
      name,
      description,
      price,
      capacity,
      facilities,
      status,
      image_url,
    });
    res.status(201).json({ message: "Room created successfully", room });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Internal Server Error", error: error.message });
  }
};

export const updateRoom = async (req, res) => {
  try {
    const {
      name,
      description,
      price,
      capacity,
      facilities,
      status,
      image_url,
    } = req.body;
    const room = await Room.findByPk(req.params.id);
    if (!room) {
      return res.status(404).json({ message: "Room not found" });
    }

    await room.update({
      name,
      description,
      price,
      capacity,
      facilities,
      status,
      image_url,
    });

    res.json({ message: "Room updated successfully", room });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Internal Server Error" });
  }
};

export const deleteRoom = async (req, res) => {
  try {
    const room = await Room.findByPk(req.params.id);
    if (!room) {
      return res.status(404).json({ message: "Room not found" });
    }

    await room.destroy();
    res.json({ message: "Room deleted successfully" });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Internal Server Error" });
  }
};
