import Booking from "../models/booking.js";
import User from "../models/user.js";
import Room from "../models/room.js";
import { Op } from "sequelize";

// Function to create a new booking
export const createBooking = async (req, res) => {
  try {
    const { user_id, room_id, date, start_date, end_date, total_price, notes } =
      req.body;

    // Check if user and room exist
    const user = await User.findByPk(user_id);
    const room = await Room.findByPk(room_id);

    if (!user) {
      return res.status(400).json({ msg: "User not found" });
    }

    if (!room) {
      return res.status(400).json({ msg: "Room not found" });
    }

    const booking = await Booking.create({
      user_id,
      room_id,
      date,
      start_date,
      end_date,
      total_price,
      notes,
    });

    res.status(201).json({ msg: "Booking created", booking });
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to create booking", error: error.message });
  }
};

// Function to get all bookings
export const getAllBookings = async (req, res) => {
  try {
    const bookings = await Booking.findAll({
      include: [
        { model: User, as: "user" },
        { model: Room, as: "room" },
      ],
    });
    res.status(200).json(bookings);
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to retrieve bookings", error: error.message });
  }
};

// Function to get a booking by ID
export const getBookingById = async (req, res) => {
  try {
    const booking = await Booking.findByPk(req.params.id, {
      include: [
        { model: User, as: "user" },
        { model: Room, as: "room" },
      ],
    });
    if (booking) {
      res.status(200).json(booking);
    } else {
      res.status(404).json({ msg: "Booking not found" });
    }
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to retrieve booking", error: error.message });
  }
};

// Function to update a booking
export const updateBooking = async (req, res) => {
  try {
    const { status, notes } = req.body;

    const booking = await Booking.findByPk(req.params.id);

    if (!booking) {
      return res.status(404).json({ msg: "Booking not found" });
    }

    await booking.update({
      status,
      notes,
    });

    res.status(200).json({ msg: "Booking updated", booking });
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to update booking", error: error.message });
  }
};

// Function to delete a booking
export const deleteBooking = async (req, res) => {
  try {
    const booking = await Booking.findByPk(req.params.id);
    if (!booking) {
      return res.status(404).json({ msg: "Booking not found" });
    }

    await booking.destroy();
    res.status(200).json({ msg: "Booking deleted" });
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to delete booking", error: error.message });
  }
};

// Function to get bookings by user ID
export const getBookingsByUserId = async (req, res) => {
  try {
    const user_id = req.params.userId;
    const bookings = await Booking.findAll({
      where: { user_id: user_id },
      include: [{ model: Room, as: "room" }],
    });
    if (bookings) {
      res.status(200).json(bookings);
    } else {
      res.status(404).json({ msg: "No bookings found for this user" });
    }
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to retrieve bookings", error: error.message });
  }
};

// Function to get bookings by room ID
export const getBookingsByRoomId = async (req, res) => {
  try {
    const room_id = req.params.roomId;
    const bookings = await Booking.findAll({
      where: { room_id: room_id },
      include: [{ model: User, as: "user" }],
    });
    if (bookings) {
      res.status(200).json(bookings);
    } else {
      res.status(404).json({ msg: "No bookings found for this room" });
    }
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to retrieve bookings", error: error.message });
  }
};

// Function to get bookings by date range
export const getBookingsByDateRange = async (req, res) => {
  try {
    const { startDate, endDate } = req.query;

    if (!startDate || !endDate) {
      return res
        .status(400)
        .json({ msg: "Start date and end date are required" });
    }

    const bookings = await Booking.findAll({
      where: {
        date: {
          [Op.between]: [startDate, endDate],
        },
      },
      include: [
        { model: User, as: "user" },
        { model: Room, as: "room" },
      ],
    });

    res.status(200).json(bookings);
  } catch (error) {
    res
      .status(500)
      .json({ msg: "Failed to retrieve bookings", error: error.message });
  }
};
