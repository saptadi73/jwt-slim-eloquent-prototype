-- PostgreSQL: Create product_order_lines table
-- Run in DBeaver connected to your erpmini database FIRST before hpp migration

CREATE TABLE IF NOT EXISTS product_order_lines (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    sale_order_id UUID NOT NULL,
    product_id UUID NOT NULL,
    line_number INTEGER,
    description VARCHAR(255),
    qty DECIMAL(15, 2) DEFAULT 0,
    unit_price DECIMAL(15, 2) DEFAULT 0,
    discount DECIMAL(15, 2) DEFAULT 0,
    line_total DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_product_order_lines_sale_order 
        FOREIGN KEY (sale_order_id) 
        REFERENCES sale_orders(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_product_order_lines_product 
        FOREIGN KEY (product_id) 
        REFERENCES products(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_product_order_lines_sale_order_id ON product_order_lines(sale_order_id);
CREATE INDEX IF NOT EXISTS idx_product_order_lines_product_id ON product_order_lines(product_id);

-- Create trigger for auto-update updated_at
CREATE OR REPLACE FUNCTION update_product_order_lines_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_product_order_lines_timestamp
BEFORE UPDATE ON product_order_lines
FOR EACH ROW
EXECUTE FUNCTION update_product_order_lines_timestamp();
