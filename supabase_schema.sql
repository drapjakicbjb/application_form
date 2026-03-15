-- Dr. A.P.J. Abdul Kalam School Admission System
-- Supabase SQL Schema for Static Migration

-- 1. Create the Applications Table
CREATE TABLE IF NOT EXISTS public.applications (
    -- Primary Key
    id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    
    -- Student Details
    student_name TEXT NOT NULL,
    dob DATE NOT NULL,
    gender TEXT NOT NULL,
    blood_group TEXT,
    aadhaar TEXT NOT NULL,
    class_applied TEXT NOT NULL,
    stream TEXT,
    previous_school TEXT,
    
    -- Contact Details
    father_name TEXT NOT NULL,
    mother_name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    pincode TEXT NOT NULL,
    
    -- Document storage (Base64 Bytea or Text)
    -- We use TEXT columns to store Base64 strings directly as handled by script.js
    photo TEXT,
    photo_mime TEXT,
    aadhaar_front TEXT,
    aadhaar_front_mime TEXT,
    aadhaar_back TEXT,
    aadhaar_back_mime TEXT,
    marksheet TEXT,
    marksheet_mime TEXT,
    
    -- Administrative fields
    status TEXT DEFAULT 'Pending' CHECK (status IN ('Pending', 'Accepted', 'Rejected')),
    created_at TIMESTAMPTZ DEFAULT now()
);

-- 2. Enable Row Level Security (RLS)
ALTER TABLE public.applications ENABLE ROW LEVEL SECURITY;

-- 3. Create RLS Policies

-- Policy: Allow anyone to submit an application (Anonymous Insert)
CREATE POLICY "Enable insert for authenticated users only" ON public.applications 
    FOR INSERT 
    WITH CHECK (true);

-- Policy: Allow admins to view/manage all applications
-- (Assumes admins are authenticated users in Supabase Auth)
CREATE POLICY "Enable full access for authenticated users" ON public.applications 
    FOR ALL 
    TO authenticated
    USING (auth.uid() IS NOT NULL);

-- 4. Enable useful extensions (Optional but recommended)
-- CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- 5. Helper Comment for Admin Access
-- To give someone access, create a user in Authentication > Users 
-- and ensure they are assigned the 'authenticated' role.
