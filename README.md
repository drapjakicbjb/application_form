# 🏫 Online School Admission System

A modern, high-performance admission portal built with **HTML5 + TailwindCSS + JavaScript + Supabase**. This version is fully **static**, making it compatible with GitHub Pages, Netlify, or Vercel.

---

## 📁 Project Structure

```
Admission Form/
├── index.html              → Public admission form (Static)
├── style.css               → Custom CSS (glassmorphism, animations)
├── script.js               → Validation, Supabase integration, file handling
├── supabase_schema.sql     → FULL database schema for Supabase
├── confirmation.html       → Success & confirmation page
└── admin/
    ├── login.html           → Admin login (Supabase Auth)
    ├── dashboard.html       → Live stats & recent applications
    └── view_applications.html → Full application management panel
```

---

## ⚙️ Setup Instructions (Supabase)

### Step 1 — Create a Supabase Project
1. Go to [Supabase.com](https://supabase.com/) and create a new project.
2. Note your **Project URL** and **Anon Key** from the Settings > API tab.

### Step 2 — Initialize Database
1. Go to the **SQL Editor** in your Supabase dashboard.
2. Open the `supabase_schema.sql` file from this project.
3. Copy and paste the content into the SQL Editor and click **Run**.
4. This will create the `applications` table and set up Row Level Security (RLS).

### Step 3 — Update API Keys
1. Open `script.js`, `admin/login.html`, `admin/dashboard.html`, and `admin/view_applications.html`.
2. Replace the `SUPABASE_URL` and `SUPABASE_KEY` constants at the top of these files with your own project credentials.

### Step 4 — Set Up Admin User
1. Go to **Authentication** > **Users** in Supabase.
2. Click **Add User** > **Create new user**.
3. Use the **Username** as the email part (e.g., `admin@yoursite.com`) and set your password.

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
