-- ========================================
-- SQL Commands for Positions, Attendance, and TimeOff Tables
-- Database: PostgreSQL
-- Generated: 30 Desember 2025
-- ========================================

-- 1. CREATE TABLE positions
CREATE TABLE IF NOT EXISTS positions (
    id UUID PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 2. CREATE TABLE time_offs (leave requests / cuti)
CREATE TABLE IF NOT EXISTS time_offs (
    id SERIAL PRIMARY KEY,
    pegawai_id UUID,
    employee_id UUID,
    type VARCHAR(255) DEFAULT 'annual_leave' CHECK (type IN ('annual_leave', 'sick_leave', 'unpaid_leave', 'maternity_leave', 'paternity_leave', 'other')),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INTEGER,
    reason TEXT,
    status VARCHAR(255) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'cancelled')),
    approved_by UUID,
    approved_at TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES pegawai(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES pegawai(id) ON DELETE SET NULL
);

-- Create indexes for time_offs
CREATE INDEX idx_time_offs_pegawai_id ON time_offs(pegawai_id);
CREATE INDEX idx_time_offs_employee_id ON time_offs(employee_id);
CREATE INDEX idx_time_offs_start_date ON time_offs(start_date);
CREATE INDEX idx_time_offs_status ON time_offs(status);

-- 3. CREATE TABLE attendances (presensi)
CREATE TABLE IF NOT EXISTS attendances (
    id SERIAL PRIMARY KEY,
    pegawai_id UUID,
    employee_id UUID,
    date DATE NOT NULL,
    check_in TIMESTAMP,
    check_out TIMESTAMP,
    status VARCHAR(255) DEFAULT 'absent' CHECK (status IN ('present', 'absent', 'late', 'half_day', 'on_leave', 'sick', 'holiday')),
    work_hours NUMERIC(5,2),
    overtime_hours NUMERIC(5,2),
    notes TEXT,
    location VARCHAR(255),
    check_in_photo VARCHAR(255),
    check_out_photo VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES pegawai(id) ON DELETE CASCADE
);

-- Create indexes for attendances
CREATE INDEX idx_attendances_pegawai_id ON attendances(pegawai_id);
CREATE INDEX idx_attendances_employee_id ON attendances(employee_id);
CREATE INDEX idx_attendances_date ON attendances(date);
CREATE INDEX idx_attendances_pegawai_date ON attendances(pegawai_id, date);
CREATE INDEX idx_attendances_employee_date ON attendances(employee_id, date);

-- ========================================
-- NOTES:
-- ========================================
-- 1. positions table:
--    - UUID primary key
--    - nama (VARCHAR 255) untuk nama posisi/jabatan
--
-- 2. time_offs table:
--    - SERIAL (auto-increment) primary key
--    - pegawai_id dan employee_id untuk backward compatibility
--    - type: jenis cuti (annual_leave, sick_leave, dll)
--    - status: pending, approved, rejected, cancelled
--    - approved_by: siapa yang approve (FK ke pegawai)
--
-- 3. attendances table:
--    - SERIAL (auto-increment) primary key
--    - pegawai_id dan employee_id untuk backward compatibility
--    - status: present, absent, late, half_day, on_leave, sick, holiday
--    - work_hours dan overtime_hours dalam desimal (jam)
--    - check_in_photo dan check_out_photo untuk foto bukti
--
-- ========================================
