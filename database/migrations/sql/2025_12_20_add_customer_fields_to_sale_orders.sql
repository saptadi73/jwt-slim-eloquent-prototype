-- PostgreSQL: Add snapshot customer fields to sale_orders
-- Run in DBeaver connected to your erpmini database

ALTER TABLE sale_orders
    ADD COLUMN IF NOT EXISTS nama VARCHAR(255),
    ADD COLUMN IF NOT EXISTS alamat TEXT,
    ADD COLUMN IF NOT EXISTS hp VARCHAR(30),
    ADD COLUMN IF NOT EXISTS keterangan TEXT;

-- Optional: indexes for quick filtering/search
CREATE INDEX IF NOT EXISTS idx_sale_orders_nama ON sale_orders(nama);
CREATE INDEX IF NOT EXISTS idx_sale_orders_hp ON sale_orders(hp);
