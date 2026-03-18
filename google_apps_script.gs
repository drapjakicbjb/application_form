/**
 * Google Apps Script Backend for School Admission System
 * This script should be deployed as a Web App (Execute as: Me, Access: Anyone)
 */

const SHEET_NAME = 'Applications';
const ADMIN_SHEET_NAME = 'Admins';
const FOLDER_ID = '1LF2HEDtvxsRJ6ICbuUDYP9NoRwbn1_y1'; // User must replace this

/**
 * Handle POST requests (Form Submission)
 */
function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    let sheet = ss.getSheetByName(SHEET_NAME);
    
    // Create sheet if it doesn't exist
    if (!sheet) {
      sheet = ss.insertSheet(SHEET_NAME);
      const headers = [
        'ID', 'Status', 'Date', 'Student Name', 'DOB', 'Gender', 'Blood Group', 'Aadhaar', 'Class Applied', 'Stream', 'Previous School',
        'Father Name', 'Mother Name', 'Phone', 'Email', 'Address', 'City', 'State', 'Pincode',
        'Photo URL', 'Aadhaar Front URL', 'Aadhaar Back URL', 'Marksheet URL'
      ];
      sheet.appendRow(headers);
    }

    const lastRow = sheet.getLastRow();
    const id = lastRow === 1 ? 1 : lastRow;
    const date = new Date().toISOString();

    // Handle File Uploads to Google Drive
    let folder;
    try {
      folder = DriveApp.getFolderById(FOLDER_ID);
    } catch (e) {
      console.error("Folder ID error: " + e.message);
    }

    const ts = Utilities.formatDate(new Date(), "GMT+5:30", "yyyy-MM-dd_HH-mm-ss");
    
    const uploadFile = (base64, name, mime) => {
      if (!base64 || !folder) return '';
      try {
        const finalName = `${name}_${ts}`;
        const blob = Utilities.newBlob(Utilities.base64Decode(base64), mime, finalName);
        const file = folder.createFile(blob);
        file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
        return `https://drive.google.com/uc?export=view&id=${file.getId()}`;
      } catch (err) {
        console.error("Upload error: " + err.message);
        return '';
      }
    };

    const photoUrl = uploadFile(data.photo, `${data.student_name}_photo`, data.photo_mime);
    const aadhaarFrontUrl = uploadFile(data.aadhaar_front, `${data.student_name}_aadhaar_front`, data.aadhaar_front_mime);
    const aadhaarBackUrl = uploadFile(data.aadhaar_back, `${data.student_name}_aadhaar_back`, data.aadhaar_back_mime);
    const marksheetUrl = uploadFile(data.marksheet, `${data.student_name}_marksheet`, data.marksheet_mime);

    // Append Data to Sheet
    const row = [
      id, 'Pending', date, data.student_name, data.dob, data.gender, data.blood_group || '', data.aadhaar, data.class_applied, data.stream || '', data.previous_school || '',
      data.father_name, data.mother_name, data.phone, data.email, data.address, data.city, data.state, data.pincode,
      photoUrl, aadhaarFrontUrl, aadhaarBackUrl, marksheetUrl
    ];
    sheet.appendRow(row);

    return ContentService.createTextOutput(JSON.stringify({ success: true, id: id }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, message: err.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Verify Admin Credentials
 */
function verifyAdmin(username, password) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  let adminSheet = ss.getSheetByName(ADMIN_SHEET_NAME);
  
  if (!adminSheet) {
    adminSheet = ss.insertSheet(ADMIN_SHEET_NAME);
    adminSheet.appendRow(['Username', 'Password']);
  }
  
  const data = adminSheet.getDataRange().getValues();
  for (let i = 1; i < data.length; i++) {
    const sheetUser = String(data[i][0]).trim();
    const sheetPass = String(data[i][1]).trim();
    if (sheetUser === String(username).trim() && sheetPass === String(password).trim()) {
      return true;
    }
  }
  return false;
}

/**
 * Handle GET requests (Fetch Data for Admin)
 */
function doGet(e) {
  try {
    const action = e.parameter.action;
    const id = e.parameter.id;
    
    // Public action: get_by_id
    // This allows the confirmation page to show details to the student
    if (action === 'get_by_id' && id) {
      const ss = SpreadsheetApp.getActiveSpreadsheet();
      const sheet = ss.getSheetByName(SHEET_NAME);
      if (!sheet) return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'Sheet not found' })).setMimeType(ContentService.MimeType.JSON);
      
      const data = sheet.getDataRange().getValues();
      const headers = data.shift();
      
      for (let i = 0; i < data.length; i++) {
        if (data[i][0] == id) {
          const obj = {};
          headers.forEach((h, j) => obj[h.toString().trim().toLowerCase().replace(/ /g, '_')] = data[i][j]);
          return ContentService.createTextOutput(JSON.stringify({ success: true, data: obj }))
            .setMimeType(ContentService.MimeType.JSON);
        }
      }
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'Application not found' }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    const username = e.parameter.username;
    const password = e.parameter.password;

    if (!verifyAdmin(username, password)) {
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'Invalid Credentials' }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName(SHEET_NAME);
    if (!sheet) return ContentService.createTextOutput(JSON.stringify({ data: [] })).setMimeType(ContentService.MimeType.JSON);
    
    const data = sheet.getDataRange().getValues();
    const headers = data.shift();
    
    const jsonData = data.map(row => {
      const obj = {};
      headers.forEach((h, i) => obj[h.toString().trim().toLowerCase().replace(/ /g, '_')] = row[i]);
      return obj;
    });

    if (action === 'get_stats') {
      const stats = {
        total: jsonData.length,
        pending: jsonData.filter(i => i.status === 'Pending').length,
        accepted: jsonData.filter(i => i.status === 'Accepted').length
      };
      return ContentService.createTextOutput(JSON.stringify({ success: true, stats: stats }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    if (action === 'update_status') {
      const id = e.parameter.id;
      const newStatus = e.parameter.status;
      const range = sheet.getDataRange();
      const values = range.getValues();
      for (let i = 1; i < values.length; i++) {
        if (values[i][0] == id) {
          sheet.getRange(i + 1, 2).setValue(newStatus); // Status is column B (2)
          return ContentService.createTextOutput(JSON.stringify({ success: true }))
            .setMimeType(ContentService.MimeType.JSON);
        }
      }
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'ID not found' }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    if (action === 'delete_application') {
      const id = e.parameter.id;
      const values = sheet.getDataRange().getValues();
      for (let i = 1; i < values.length; i++) {
        if (values[i][0] == id) {
          sheet.deleteRow(i + 1);
          return ContentService.createTextOutput(JSON.stringify({ success: true }))
            .setMimeType(ContentService.MimeType.JSON);
        }
      }
      return ContentService.createTextOutput(JSON.stringify({ success: false, message: 'ID not found' }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    return ContentService.createTextOutput(JSON.stringify({ success: true, data: jsonData }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, message: err.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
