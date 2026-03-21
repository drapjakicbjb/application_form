/**
 * Google Apps Script - School Admission System (Infallible Version)
 * Deployment: Web App (Me / Anyone)
 */

// 1. CONFIGURATION
const SPREADSHEET_ID = '1Cn2w-vcr-I8ges2NS8LOCNlzgQWpKvFou1DvLrRJl-I'; // Optional: Paste your Sheet ID here if seeing connection errors
const FOLDER_ID = '1LF2HEDtvxsRJ6ICbuUDYP9NoRwbn1_y1'; 
const ADMIN_SHEET = 'Admins';
const CONFIG_SHEET = 'System_Config';

const LEVEL_SHEETS = {
  'primary': 'Primary_Admissions',
  'middle': 'Middle_Admissions',
  'secondary': 'Secondary_Higher_Admissions',
  'higher_secondary': 'Secondary_Higher_Admissions'
};

const APP_HEADERS = [
  'ID', 'Status', 'Date', 'School Level', 'Student Name', 'DOB', 'Gender', 'Religion', 'Category', 'Caste', 
  'Blood Group', 'Aadhaar', 'Class Applied', 'Medium', 'Stream', 'Previous School',
  'Father Name', 'Mother Name', 'Phone', 'Email', 'Address', 'City', 'State', 'Pincode',
  'Photo URL', 'Aadhaar Front URL', 'Aadhaar Back URL', 'Marksheet URL'
];

// --- CORE FUNCTIONS ---

/**
 * Robust Spreadsheet Connection
 */
function getSS() {
  let ss = null;
  if (SPREADSHEET_ID && SPREADSHEET_ID.length > 5) {
    try { ss = SpreadsheetApp.openById(SPREADSHEET_ID); } catch(e) {}
  }
  
  if (!ss) {
    try { ss = SpreadsheetApp.getActiveSpreadsheet(); } catch(e) {}
  }
  
  if (!ss) {
    try { ss = SpreadsheetApp.getActive(); } catch(e) {}
  }

  if (!ss) {
    throw new Error("Spreadsheet NOT FOUND. Please provide SPREADSHEET_ID or bind script to long Sheet.");
  }
  return ss;
}

/**
 * Get or Create a sheet safely
 */
function getSheet(sheetName, headers) {
  const ss = getSS();
  let sheet = ss.getSheetByName(sheetName);
  
  if (!sheet) {
    sheet = ss.insertSheet(sheetName);
    if (headers) {
      sheet.appendRow(headers);
      sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold').setBackground('#f1f5f9');
    }
  } else if (headers && sheet.getLastRow() === 0) {
    sheet.appendRow(headers);
    sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold').setBackground('#f1f5f9');
  }
  return sheet;
}

/**
 * Global ID counter
 */
function getNextGlobalId() {
  const config = getSheet(CONFIG_SHEET, ['Key', 'Value']);
  const data = config.getDataRange().getValues();
  let lastId = 0;
  let rowIdx = -1;

  for (let i = 1; i < data.length; i++) {
    if (data[i][0] === 'Last_ID') {
      lastId = parseInt(data[i][1]) || 0;
      rowIdx = i + 1;
      break;
    }
  }

  const nextId = lastId + 1;
  if (rowIdx === -1) {
    config.appendRow(['Last_ID', nextId]);
  } else {
    config.getRange(rowIdx, 2).setValue(nextId);
  }
  return nextId;
}

/**
 * File Uploads
 */
function uploadToDrive(base64, fileName, mimeType) {
  if (!base64 || !FOLDER_ID) return '';
  try {
    const folder = DriveApp.getFolderById(FOLDER_ID);
    const ts = Utilities.formatDate(new Date(), "GMT+5:30", "yyyy-MM-dd_HH-mm-ss");
    const blob = Utilities.newBlob(Utilities.base64Decode(base64), mimeType, `${fileName}_${ts}`);
    const file = folder.createFile(blob);
    file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
    return `https://drive.google.com/uc?export=view&id=${file.getId()}`;
  } catch (err) {
    return '';
  }
}

// --- ENDPOINTS ---

function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: "No data received. Manual Run not supported." })).setMimeType(ContentService.MimeType.JSON);
    }

    const data = JSON.parse(e.postData.contents);
    const targetName = LEVEL_SHEETS[data.school_level] || 'Other_Admissions';
    const sheet = getSheet(targetName, APP_HEADERS);

    const id = getNextGlobalId();
    const date = new Date().toISOString();

    const photo = uploadToDrive(data.photo, `${data.student_name}_photo`, data.photo_mime);
    const adf = uploadToDrive(data.aadhaar_front, `${data.student_name}_af`, data.aadhaar_front_mime);
    const adb = uploadToDrive(data.aadhaar_back, `${data.student_name}_ab`, data.aadhaar_back_mime);
    const mark = uploadToDrive(data.marksheet, `${data.student_name}_mark`, data.marksheet_mime);

    // Map data to row based on APP_HEADERS to prevent shifting
    const row = APP_HEADERS.map(header => {
      // Convert header name to lowercase key for mapping (e.g., 'Student Name' -> 'student_name')
      // Special logic for keys that don't match exactly
      if (header === 'ID') return id;
      if (header === 'Status') return 'Pending';
      if (header === 'Date') return date;
      if (header === 'School Level') return data.school_level || '';
      if (header === 'Photo URL') return photo;
      if (header === 'Aadhaar Front URL') return adf;
      if (header === 'Aadhaar Back URL') return adb;
      if (header === 'Marksheet URL') return mark;

      // Generic mapping: "Student Name" -> "student_name"
      const key = header.toLowerCase().replace(/\s+/g, '_');
      return data[key] !== undefined ? data[key] : '';
    });

    sheet.appendRow(row);
    return ContentService.createTextOutput(JSON.stringify({ success: true, id: id })).setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, message: err.toString() })).setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  try {
    if (!e || !e.parameter) {
       return ContentService.createTextOutput(JSON.stringify({ success: false, message: "No parameters." })).setMimeType(ContentService.MimeType.JSON);
    }

    const action = e.parameter.action;
    const id = e.parameter.id;

    if (action === 'get_by_id' && id) {
      const all = aggregateAll();
      const app = all.find(a => a.id == id);
      return ContentService.createTextOutput(JSON.stringify({ success: !!app, data: app })).setMimeType(ContentService.MimeType.JSON);
    }

    if (!verifyAdmin(e.parameter.username, e.parameter.password)) {
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'Unauthorized' })).setMimeType(ContentService.MimeType.JSON);
    }

    const all = aggregateAll();

    if (action === 'get_stats') {
      return ContentService.createTextOutput(JSON.stringify({
        success: true,
        stats: {
          total: all.length,
          pending: all.filter(a => a.status === 'Pending').length,
          accepted: all.filter(a => a.status === 'Accepted').length
        }
      })).setMimeType(ContentService.MimeType.JSON);
    }

    if (action === 'update_status') {
      const status = e.parameter.status;
      const success = updateRow(id, 2, status);
      return ContentService.createTextOutput(JSON.stringify({ success: success })).setMimeType(ContentService.MimeType.JSON);
    }

    if (action === 'delete_application') {
      const success = deleteRowById(id);
      return ContentService.createTextOutput(JSON.stringify({ success: success })).setMimeType(ContentService.MimeType.JSON);
    }

    return ContentService.createTextOutput(JSON.stringify({ success: true, data: all })).setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, message: err.toString() })).setMimeType(ContentService.MimeType.JSON);
  }
}

// --- HELPERS ---

function verifyAdmin(user, pass) {
  if (!user || !pass) return false;
  const sheet = getSheet(ADMIN_SHEET, ['Username', 'Password']);
  const data = sheet.getDataRange().getValues();
  if (data.length <= 1) {
    sheet.appendRow(['admin', '12345']);
    return (user === 'admin' && pass === '12345');
  }
  for (let i = 1; i < data.length; i++) {
    if (String(data[i][0]).trim() === String(user).trim() && String(data[i][1]).trim() === String(pass).trim()) return true;
  }
  return false;
}

function aggregateAll() {
  const ss = getSS();
  const all = [];
  const sheets = ss.getSheets();
  const reserved = [ADMIN_SHEET, CONFIG_SHEET];

  sheets.forEach(s => {
    const name = s.getName();
    if (reserved.includes(name)) return;
    const vals = s.getDataRange().getValues();
    if (vals.length <= 1) return;
    const heads = vals.shift();
    vals.forEach(row => {
      const obj = {};
      heads.forEach((h, i) => {
        const key = h.toString().trim().toLowerCase().replace(/ /g, '_');
        obj[key] = row[i];
      });
      all.push(obj);
    });
  });
  return all.sort((a, b) => new Date(b.date) - new Date(a.date));
}

function updateRow(id, col, val) {
  const ss = getSS();
  const sheets = ss.getSheets();
  const reserved = [ADMIN_SHEET, CONFIG_SHEET];
  for (const s of sheets) {
    if (reserved.includes(s.getName())) continue;
    const data = s.getDataRange().getValues();
    for (let i = 1; i < data.length; i++) {
      if (data[i][0] == id) {
        s.getRange(i + 1, col).setValue(val);
        return true;
      }
    }
  }
  return false;
}

function deleteRowById(id) {
  const ss = getSS();
  const sheets = ss.getSheets();
  const reserved = [ADMIN_SHEET, CONFIG_SHEET];
  for (const s of sheets) {
    if (reserved.includes(s.getName())) continue;
    const data = s.getDataRange().getValues();
    for (let i = 1; i < data.length; i++) {
      if (data[i][0] == id) {
        s.deleteRow(i + 1);
        return true;
      }
    }
  }
  return false;
}