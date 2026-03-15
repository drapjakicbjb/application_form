# 🏫 Online School Admission System

A modern, high-performance admission portal built with **HTML5 + TailwindCSS + JavaScript + Supabase**. This version is fully **static**, making it compatible with GitHub Pages, Netlify, or Vercel.

---

## 📁 Project Structure

```
Admission Form/
# School Admission System (Google Sheets Edition)

This is a modern, static web-based school admission system that stores all data in **Google Sheets** and documents in **Google Drive**. No expensive servers or database subscriptions required!

## 🚀 Features
- **Static Website**: Host it anywhere (GitHub Pages, Vercel, Netlify).
- **Google Sheets Database**: Direct access to your data via a spreadsheet.
- **Google Drive Storage**: Photos, Aadhaar cards, and marksheets are safely stored in your own Drive.
- **Admin Dashboard**: Password-protected management panel for reviewing and managing applications.
- **Multi-language**: Fully supports English and Hindi.

## 🛠️ Setup Instructions

### 1. Google Drive & Sheets Setup
1. Create a new folder in your Google Drive (e.g., "School Admissions"). **Copy the Folder ID** from the URL.
2. Create a new Google Sheet inside that folder.
3. In the Google Sheet, go to **Extensions > Apps Script**.
4. Copy the code from `google_apps_script.gs` and paste it into the editor.
5. Replace `YOUR_GOOGLE_DRIVE_FOLDER_ID` with your actual folder ID.
6. Click **Deploy > New Deployment**.
7. Select **Web App**.
   - **Execute as**: Me
   - **Who has access**: Anyone
8. Copy the **Web App URL**.

### 2. Website Configuration
1. Open `script.js` and replace `YOUR_GOOGLE_SCRIPT_WEB_APP_URL` with your Web App URL.
2. Open `admin/login.html`, `admin/dashboard.html`, and `admin/view_applications.html` and replace the placeholder URL there as well.
3. Open `confirmation.html` and update the URL there.

### 3. Deploy Website
Simply upload all files to your static hosting provider (like GitHub Pages).

## 🔑 Admin Access
- The default password is set in `google_apps_script.gs`.
- URL: `your-site.com/admin/login.html`

## 🗂️ File Structure
- `index.html`: Main admission form.
- `script.js`: Frontend logic and form submission.
- `google_apps_script.gs`: Backend code for Google Workspace.
- `admin/`: Dashboard and management pages.
- `assets/`: Static school assets like logos.

---

## 🚀 Deployment

Since this project is purely static, you can deploy it anywhere:

1. **GitHub Pages**: Simply upload the files to a repository and enable "Pages" in settings.
2. **Netlify**: Drag and drop the folder into the Netlify dashboard.
3. **Vercel**: Link your GitHub repo to Vercel for automatic deployment.

---

## ✨ Features

| Feature | Details |
|---------|---------|
| Static Architecture | No backend server required (compatible with GitHub Pages) |
| Supabase Integration | Real-time data storage and secure authentication |
| Multi-language | Full support for English and Hindi (i18n) |
| Progress Bar | Animated indicator with step-by-step tracking |
| Power Search | Filter by status (Pending/Accepted/Rejected) and view details |
| File Management | Documents stored as Base64 in Supabase for simplicity |
| Print Mode | Optimized CSS for printing student applications |

---

## 🔐 Security

1. **Row Level Security (RLS)** — Protects your data from unauthorized API access.
2. **Supabase Auth** — Secure login session management.
3. **Aadhaar Masking** — Privacy-focused display of sensitive IDs.

---

*Built with ❤️ for Dr. A.P.J. Abdul Kalam Inter College*
