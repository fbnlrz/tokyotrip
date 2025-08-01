<?php
// includes/database.php

// Function to connect to the SQLite database
function get_db_connection() {
    $db_path = __DIR__ . '/../travel_app.db';
    try {
        $pdo = new PDO('sqlite:' . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
    return $pdo;
}

// Function to initialize the database schema
function initialize_database() {
    $pdo = get_db_connection();

    $commands = [
        // Users table
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        );',

        // Trips table
        'CREATE TABLE IF NOT EXISTS trips (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_name TEXT NOT NULL,
            start_date TEXT NOT NULL,
            end_date TEXT NOT NULL
        );',

        // Trip members table (tracks participation)
        'CREATE TABLE IF NOT EXISTS trip_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );',

        // Itinerary table
        'CREATE TABLE IF NOT EXISTS itinerary (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL,
            date TEXT NOT NULL,
            activity_description TEXT NOT NULL,
            location TEXT,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
        );',

        // Bookings table
        'CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL,
            type TEXT NOT NULL, -- "flight", "hotel", "transport"
            details TEXT NOT NULL, -- JSON string
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
        );',

        // Expenses table
        'CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL,
            payer_user_id INTEGER NOT NULL,
            amount REAL NOT NULL,
            currency TEXT NOT NULL DEFAULT "JPY",
            description TEXT NOT NULL,
            is_shared BOOLEAN NOT NULL DEFAULT 0,
            -- For simplicity, split details can be handled in application logic or a separate table later
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            FOREIGN KEY (payer_user_id) REFERENCES users(id) ON DELETE CASCADE
        );',

        // Packing lists table
        'CREATE TABLE IF NOT EXISTS packing_lists (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER NOT NULL,
            list_name TEXT NOT NULL,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
        );',

        // Packing list items table
        'CREATE TABLE IF NOT EXISTS packing_list_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            list_id INTEGER NOT NULL,
            item_name TEXT NOT NULL,
            is_checked BOOLEAN NOT NULL DEFAULT 0,
            FOREIGN KEY (list_id) REFERENCES packing_lists(id) ON DELETE CASCADE
        );'
    ];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }
}

// Automatically initialize the database when this file is included
initialize_database();
?>
