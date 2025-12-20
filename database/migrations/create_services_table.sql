-- PostgreSQL SQL Command to create services table
-- Run this in DBeaver for PostgreSQL

CREATE TABLE IF NOT EXISTS services (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kategori_id UUID,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15, 2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_services_kategori 
        FOREIGN KEY (kategori_id) 
        REFERENCES kategori(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- Create index for better query performance
CREATE INDEX idx_services_kategori_id ON services(kategori_id);
CREATE INDEX idx_services_nama ON services(nama);

-- Create trigger to auto-update updated_at timestamp
CREATE OR REPLACE FUNCTION update_services_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_services_timestamp
BEFORE UPDATE ON services
FOR EACH ROW
EXECUTE FUNCTION update_services_timestamp();

-- Insert sample data (optional)
-- INSERT INTO services (kategori_id, nama, deskripsi, harga) VALUES
-- ('uuid-here', 'Service Name', 'Service Description', 50000);
