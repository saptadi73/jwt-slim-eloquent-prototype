-- PostgreSQL: Add hpp column to product_order_lines
-- Run in DBeaver connected to your erpmini database
-- NOTE: Run this AFTER creating the table with 2025_12_20_create_product_order_lines_table.sql

ALTER TABLE product_order_lines
    ADD COLUMN IF NOT EXISTS hpp DECIMAL(15, 2);

-- Optional: Index for accounting journal queries
CREATE INDEX IF NOT EXISTS idx_product_order_lines_hpp ON product_order_lines(hpp);
