# School Admission System - Setup Guide

This guide explains how to manually set up your Google Sheets and Apps Script to ensure the admission system works correctly.

## 1. Google Spreadsheet Setup
1. Create a new Google Spreadsheet.
2. In the Spreadsheet, create the following sheets with exactly these names:
   - `Primary_Admissions`
   - `Middle_Admissions`
   - `Secondary_Higher_Admissions`
   - `Admins` (For your login credentials)
   - `System_Config` (To track unique Student IDs)

### Column Headers (Important)
For **all Admission sheets** (`Primary`, `Middle`, `Secondary_Higher`), copy and paste these exact headers in row 1:

`ID`, `Status`, `Date`, `School Level`, `Student Name`, `DOB`, `Gender`, `Religion`, `Caste`, `Blood Group`, `Aadhaar`, `Class Applied`, `Medium`, `Stream`, `Previous School`, `Father Name`, `Mother Name`, `Phone`, `Email`, `Address`, `City`, `State`, `Pincode`, `Photo URL`, `Aadhaar Front URL`, `Aadhaar Back URL`, `Marksheet URL`

---

## 2. Admin Credentials
In the `Admins` sheet:
1. Row 1: `Username`, `Password`
2. Row 2: `admin`, `12345` (Use these to log in at `admin/login.html`)

---

## 3. System ID Setup
In the `System_Config` sheet:
1. Row 1: `Key`, `Value`
2. Row 2: `Last_ID`, `0`

---

## 4. Google Apps Script Deployment
1. In your Spreadsheet, go to **Extensions** > **Apps Script**.
2. Paste the code from `google_apps_script.gs` into the editor.
3. **If you are seeing errors**: Find the `const SPREADSHEET_ID = '';` line at the top.
   - Go to your Spreadsheet URL in the browser.
   - Copy the ID part: `https://docs.google.com/spreadsheets/d/[COPY_THIS_ID]/edit`
   - Paste it inside the quotes: `const SPREADSHEET_ID = 'your_sheet_id_here';`

### Deployment Steps:
1. Click **Deploy** > **New Deployment**.
2. Select type: **Web App**.
3. **Execute as**: Me (your email).
4. **Who has access**: **Anyone**.
5. Click **Deploy**.
6. Copy the **Web App URL**.
7. Go to `script.js` and update the `GOOGLE_SCRIPT_URL` at the top.

---

## 5. Google Drive Folder
1. Create a folder in Google Drive for uploads.
2. Open the folder and copy its ID from the URL: `https://drive.google.com/drive/folders/[COPY_THIS_ID]`
3. In `google_apps_script.gs`, update the `FOLDER_ID` value at the top.

---

## Troubleshooting
- **Permission Denied**: Ensure you authorized the script when deploying.
- **Undefined Sheet**: Double-check the sheet names match exactly (Caps matters).
- **Files failing to upload**: Ensure the `FOLDER_ID` is correct and you have enough storage in your Google Drive.
