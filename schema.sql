CREATE TABLE IF NOT EXISTS contact_messages (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    message_new TEXT NOT NULL,
    submitted_at TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS repair_requests (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    unit TEXT NOT NULL,
    description TEXT NOT NULL,
    submitted_at TIMESTAMP NOT NULL
);
