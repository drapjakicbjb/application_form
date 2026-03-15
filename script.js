/**
 * script.js — Admission Form JavaScript
 * Features: Language i18n (English/Hindi), Validation, Progress Bar,
 *           Dark Mode, Auto-Save, File Upload UX, Success Modal, Loading Overlay
 */

// =============================================
// 1. CONFIG & GOOGLE APP SCRIPT INIT
// =============================================
const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbzVz4w18ShRebQeUjKkS2xsYz8AzxoEsNG0Sb0pzWnmgadrJlA6xrySVbLY198gLOyP/exec'; // USER: Replace after deploying GAS

// =============================================
// 2. TRANSLATIONS (English + Hindi)
// =============================================
const translations = {
  en: {
    // Header
    'header.title': 'School Admission Portal',
    'header.year': 'Academic Year 2026–26',
    'header.admin': 'Admin',
    // Hero
    'hero.badge': 'Admissions Open',
    'hero.title': 'Apply for Admission',
    'hero.subtitle': 'Fill out the form below carefully. All fields marked',
    'hero.mandatory': 'are mandatory.',
    // Progress
    'progress.label': 'Application Progress',
    'step.student': 'Student',
    'step.contact': 'Contact',
    'step.documents': 'Documents',
    // Section 1
    's1.title': 'Student Details',
    's1.sub': 'Basic information about the student',
    'f.student_name': 'Full Name',
    'f.dob': 'Date of Birth',
    'f.gender': 'Gender',
    'f.blood_group': 'Blood Group',
    'f.aadhaar': 'Aadhaar Number',
    'f.class_applied': 'Class Applying For',
    'f.previous_school': 'Previous School',
    // Section 2
    's2.title': 'Contact Details',
    's2.sub': 'Parent / Guardian information',
    'f.father_name': "Father's Name",
    'f.mother_name': "Mother's Name",
    'f.phone': 'Phone Number',
    'f.email': 'Email Address',
    'f.address': 'Full Address',
    'f.city': 'City',
    'f.state': 'State',
    'f.pincode': 'Pincode',
    // Section 3
    's3.title': 'Document Upload',
    's3.sub': 'Max 2 MB per file · JPG, PNG, PDF',
    'f.photo': 'Student Photo',
    'f.aadhaar_front': 'Aadhaar Card — Front Side',
    'f.aadhaar_back': 'Aadhaar Card — Back Side',
    'f.marksheet': 'Previous Marksheet',
    'upload.click': 'Click to upload',
    'upload.front': 'Front Side',
    'upload.back': 'Back Side',
    // Options
    'opt.gender_def': 'Select Gender',
    'opt.male': 'Male',
    'opt.female': 'Female',
    'opt.other': 'Other',
    'opt.blood_def': 'Select Blood Group',
    'opt.class_def': 'Select Class',
    'opt.state_def': 'Select State',
    // Buttons
    'btn.save': 'Save Draft',
    'btn.submit': 'Submit Application',
    // Declaration
    'declaration': 'I hereby declare that all information provided is true and correct to the best of my knowledge. I understand that providing false information may result in disqualification.',
    // Success / Loading
    'success.title': 'Application Submitted!',
    'success.msg': 'Your application has been received successfully.',
    'success.btn': 'Submit Another Application',
    'loading': 'Submitting Application…',
    // Placeholders
    'p.student_name': "Enter student's full name",
    'p.aadhaar': '12-digit Aadhaar number',
    'p.previous_school': 'Name of previous school (if any)',
    'p.father_name': "Father's full name",
    'p.mother_name': "Mother's full name",
    'p.phone': '10-digit mobile number',
    'p.address': 'House No., Street, Locality...',
    'p.city': 'City',
    'p.pincode': '6-digit pincode',
    // Validation errors
    'f.stream': 'Stream',
    'opt.stream_def': 'Select Stream',
    'opt.maths': 'Maths',
    'opt.home_sci': 'Home Science',
    'opt.bio': 'Biology',
    'opt.arts': 'Arts',
    'err.stream_req': 'Please select a stream.',
    'err.name_req': "Please enter student's full name.",
    'err.dob_req': 'Date of birth is required.',
    'err.dob_future': 'Date of birth cannot be in the future.',
    'err.gender_req': 'Please select gender.',
    'err.aadhaar_req': 'Aadhaar number is required.',
    'err.aadhaar_fmt': 'Aadhaar must be exactly 12 digits.',
    'err.class_req': 'Please select class applying for.',
    'err.father_req': "Father's name is required.",
    'err.mother_req': "Mother's name is required.",
    'err.phone_req': 'Phone number is required.',
    'err.phone_fmt': 'Enter a valid 10-digit Indian mobile number.',
    'err.email_req': 'Email address is required.',
    'err.email_fmt': 'Enter a valid email address.',
    'err.address_req': 'Address is required.',
    'err.city_req': 'City is required.',
    'err.state_req': 'Please select state.',
    'err.pincode_req': 'Pincode is required.',
    'err.pincode_fmt': 'Enter a valid 6-digit pincode.',
    'err.photo_req': 'Student photo is required.',
    'err.af_req': 'Aadhaar front side is required.',
    'err.ab_req': 'Aadhaar back side is required.',
    'err.decl_req': 'Please accept the declaration.',
    'toast.saved': 'Draft saved!',
  },
  hi: {
    // Header
    'header.title': 'स्कूल प्रवेश पोर्टल',
    'header.year': 'शैक्षणिक वर्ष 2026–26',
    'header.admin': 'एडमिन',
    // Hero
    'hero.badge': 'प्रवेश खुले हैं',
    'hero.title': 'प्रवेश के लिए आवेदन करें',
    'hero.subtitle': 'नीचे दिया गया फॉर्म ध्यानपूर्वक भरें। जिन क्षेत्रों में',
    'hero.mandatory': 'का चिह्न है, वे आवश्यक हैं।',
    // Progress
    'progress.label': 'आवेदन प्रगति',
    'step.student': 'छात्र',
    'step.contact': 'संपर्क',
    'step.documents': 'दस्तावेज़',
    // Section 1
    's1.title': 'छात्र विवरण',
    's1.sub': 'छात्र की मूलभूत जानकारी',
    'f.student_name': 'पूरा नाम',
    'f.dob': 'जन्म तिथि',
    'f.gender': 'लिंग',
    'f.blood_group': 'रक्त समूह',
    'f.aadhaar': 'आधार संख्या',
    'f.class_applied': 'कक्षा के लिए आवेदन',
    'f.previous_school': 'पिछला विद्यालय',
    // Section 2
    's2.title': 'संपर्क विवरण',
    's2.sub': 'अभिभावक / संरक्षक की जानकारी',
    'f.father_name': 'पिता का नाम',
    'f.mother_name': 'माता का नाम',
    'f.phone': 'फोन नंबर',
    'f.email': 'ईमेल पता',
    'f.address': 'पूरा पता',
    'f.city': 'शहर',
    'f.state': 'राज्य',
    'f.pincode': 'पिनकोड',
    // Section 3
    's3.title': 'दस्तावेज़ अपलोड',
    's3.sub': 'प्रति फ़ाइल अधिकतम 2 MB · JPG, PNG, PDF',
    'f.photo': 'छात्र की फोटो',
    'f.aadhaar_front': 'आधार कार्ड — आगे का हिस्सा',
    'f.aadhaar_back': 'आधार कार्ड — पीछे का हिस्सा',
    'f.marksheet': 'पिछली मार्कशीट',
    'upload.click': 'अपलोड करने के लिए क्लिक करें',
    'upload.front': 'आगे का हिस्सा',
    'upload.back': 'पीछे का हिस्सा',
    // Options
    'opt.gender_def': 'लिंग चुनें',
    'opt.male': 'पुरुष',
    'opt.female': 'महिला',
    'opt.other': 'अन्य',
    'opt.blood_def': 'रक्त समूह चुनें',
    'opt.class_def': 'कक्षा चुनें',
    'opt.state_def': 'राज्य चुनें',
    // Buttons
    'btn.save': 'मसौदा सहेजें',
    'btn.submit': 'आवेदन जमा करें',
    // Declaration
    'declaration': 'मैं एतद्द्वारा घोषणा करता/करती हूं कि मेरे द्वारा दी गई सभी जानकारी मेरी जानकारी के अनुसार सत्य और सही है। मैं समझता/समझती हूं कि गलत जानकारी देने पर अयोग्य घोषित किया जा सकता है।',
    // Success / Loading
    'success.title': 'आवेदन सफलतापूर्वक जमा हो गया!',
    'success.msg': 'आपका आवेदन सफलतापूर्वक प्राप्त हो गया है।',
    'success.btn': 'एक और आवेदन जमा करें',
    'loading': 'आवेदन जमा हो रहा है…',
    // Placeholders
    'p.student_name': 'छात्र का पूरा नाम दर्ज करें',
    'p.aadhaar': '12 अंकों की आधार संख्या',
    'p.previous_school': 'पिछले विद्यालय का नाम (यदि कोई हो)',
    'p.father_name': 'पिता का पूरा नाम',
    'p.mother_name': 'माता का पूरा नाम',
    'p.phone': '10 अंकों का मोबाइल नंबर',
    'p.address': 'मकान नं., गली, मोहल्ला...',
    'p.city': 'शहर',
    'p.pincode': '6 अंकों का पिनकोड',
    // Validation errors
    'f.stream': 'स्ट्रीम',
    'opt.stream_def': 'स्ट्रीम चुनें',
    'opt.maths': 'गणित',
    'opt.home_sci': 'गृह विज्ञान',
    'opt.bio': 'जीव विज्ञान',
    'opt.arts': 'कला',
    'err.stream_req': 'कृपया स्ट्रीम चुनें।',
    'err.name_req': 'कृपया छात्र का पूरा नाम दर्ज करें।',
    'err.dob_req': 'जन्म तिथि आवश्यक है।',
    'err.dob_future': 'जन्म तिथि भविष्य में नहीं हो सकती।',
    'err.gender_req': 'कृपया लिंग चुनें।',
    'err.aadhaar_req': 'आधार संख्या आवश्यक है।',
    'err.aadhaar_fmt': 'आधार संख्या ठीक 12 अंकों की होनी चाहिए।',
    'err.class_req': 'कृपया कक्षा चुनें।',
    'err.father_req': 'पिता का नाम आवश्यक है।',
    'err.mother_req': 'माता का नाम आवश्यक है।',
    'err.phone_req': 'फोन नंबर आवश्यक है।',
    'err.phone_fmt': 'वैध 10 अंकों का मोबाइल नंबर दर्ज करें।',
    'err.email_req': 'ईमेल पता आवश्यक है।',
    'err.email_fmt': 'वैध ईमेल पता दर्ज करें।',
    'err.address_req': 'पता आवश्यक है।',
    'err.city_req': 'शहर आवश्यक है।',
    'err.state_req': 'कृपया राज्य चुनें।',
    'err.pincode_req': 'पिनकोड आवश्यक है।',
    'err.pincode_fmt': 'वैध 6 अंकों का पिनकोड दर्ज करें।',
    'err.photo_req': 'छात्र की फोटो आवश्यक है।',
    'err.af_req': 'आधार कार्ड (आगे) आवश्यक है।',
    'err.ab_req': 'आधार कार्ड (पीछे) आवश्यक है।',
    'err.decl_req': 'कृपया घोषणा स्वीकार करें।',
    'toast.saved': 'मसौदा सहेजा गया!',
  }
};

// Active language
let currentLang = 'en';

// Translate helper
function t(key) {
  return (translations[currentLang] && translations[currentLang][key]) || translations['en'][key] || key;
}

// =============================================
// 2. APPLY LANGUAGE — updates all data-i18n elements
// =============================================
function applyLanguage(lang) {
  currentLang = lang;
  document.getElementById('htmlRoot').setAttribute('lang', lang === 'hi' ? 'hi' : 'en');

  // Text content
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    const val = t(key);
    if (val) el.textContent = val;
  });

  // Placeholders
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    const val = t(key);
    if (val) el.placeholder = val;
  });

  // Update select option text nodes (first option only — defaults)
  document.querySelectorAll('option[data-i18n]').forEach(opt => {
    const key = opt.getAttribute('data-i18n');
    const val = t(key);
    if (val) opt.textContent = val;
  });

  // Update language label in header
  const label = document.getElementById('currentLangLabel');
  if (label) label.textContent = lang === 'hi' ? 'हिं' : 'EN';
  document.body.style.fontFamily = lang === 'hi'
    ? "'Noto Sans Devanagari', 'Inter', sans-serif"
    : "'Inter', 'Noto Sans Devanagari', sans-serif";

  localStorage.setItem('lang', lang);
}

// =============================================
// 3. LANGUAGE MODAL LOGIC
// =============================================
function selectLanguage(lang) {
  // Animate chosen card
  const card = document.getElementById('card_' + lang);
  if (card) {
    card.style.transform = 'scale(0.95)';
    card.style.opacity = '0.8';
  }
  setTimeout(() => {
    applyLanguage(lang);
    const modal = document.getElementById('langModal');
    const content = document.getElementById('mainContent');
    // Fade out modal
    modal.style.transition = 'opacity 0.5s ease';
    modal.style.opacity = '0';
    setTimeout(() => {
      modal.classList.add('hidden');
      content.classList.remove('hidden');
      content.style.opacity = '0';
      content.style.transition = 'opacity 0.4s ease';
      requestAnimationFrame(() => { content.style.opacity = '1'; });
    }, 500);
  }, 200);
}

function showLangModal() {
  const modal = document.getElementById('langModal');
  const content = document.getElementById('mainContent');
  content.classList.add('hidden');
  modal.classList.remove('hidden');
  modal.style.opacity = '0';
  modal.style.transition = 'opacity 0.3s ease';
  requestAnimationFrame(() => { modal.style.opacity = '1'; });
}

// On load — check saved language preference
window.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('lang');
  if (saved === 'en' || saved === 'hi') {
    // Skip modal, go directly
    selectLanguage(saved);
  }
  // Otherwise modal stays visible
  loadDraft();
});

// =============================================
// 4. DARK MODE TOGGLE
// =============================================
const html = document.documentElement;
if (localStorage.getItem('theme') === 'dark' ||
  (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
  html.classList.add('dark');
}
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('darkToggle');
  if (btn) btn.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
  });
});

// =============================================
// 5. AUTO-SAVE (localStorage)
// =============================================
const AUTO_SAVE_KEY = 'admission_form_draft';
const SAVE_FIELDS = [
  'student_name', 'dob', 'gender', 'blood_group', 'aadhaar', 'class_applied', 'stream', 'previous_school',
  'father_name', 'mother_name', 'phone', 'email', 'address', 'city', 'state', 'pincode'
];

function saveDraft() {
  const data = {};
  SAVE_FIELDS.forEach(id => {
    const el = document.getElementById(id);
    if (el) data[id] = el.value;
  });
  localStorage.setItem(AUTO_SAVE_KEY, JSON.stringify(data));
  showToast(t('toast.saved'), 'success');
}

function loadDraft() {
  const saved = localStorage.getItem(AUTO_SAVE_KEY);
  if (!saved) return;
  try {
    const data = JSON.parse(saved);
    SAVE_FIELDS.forEach(id => {
      const el = document.getElementById(id);
      if (el && data[id] !== undefined) el.value = data[id];
    });
  } catch (e) { /* ignore */ }
}

// Auto-save every 30 seconds
setInterval(saveDraft, 30000);
document.addEventListener('DOMContentLoaded', () => {
  const saveBtn = document.getElementById('saveBtn');
  if (saveBtn) saveBtn.addEventListener('click', saveDraft);
});

// =============================================
// 6. STREAM DROPDOWN LOGIC
// =============================================
const STREAMS = {
  '910': ['Maths', 'Home Science'],
  '1112': ['Biology', 'Maths', 'Arts']
};
const STREAMS_HI = {
  'Maths': 'गणित',
  'Home Science': 'गृह विज्ञान',
  'Biology': 'जीव विज्ञान',
  'Arts': 'कला'
};

function updateStreamDropdown() {
  const cls = (document.getElementById('class_applied')?.value || '').trim();
  const group = document.getElementById('stream_group');
  const streamSel = document.getElementById('stream');
  if (!group || !streamSel) return;

  let options = null;
  if (cls === 'Class 9' || cls === 'Class 10') options = STREAMS['910'];
  if (cls === 'Class 11' || cls === 'Class 12') options = STREAMS['1112'];

  if (options) {
    // Rebuild options
    streamSel.innerHTML = `<option value="">${t('opt.stream_def')}</option>`;
    options.forEach(s => {
      const label = currentLang === 'hi' ? (STREAMS_HI[s] || s) : s;
      const opt = document.createElement('option');
      opt.value = s;
      opt.textContent = label;
      streamSel.appendChild(opt);
    });
    group.style.display = '';
    // Animate in
    group.style.opacity = '0';
    group.style.transform = 'translateY(-8px)';
    requestAnimationFrame(() => {
      group.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      group.style.opacity = '1';
      group.style.transform = 'translateY(0)';
    });
    streamSel.required = true;
  } else {
    group.style.display = 'none';
    streamSel.value = '';
    streamSel.required = false;
    clearError('stream');
  }
  updateProgress();
}

document.addEventListener('DOMContentLoaded', () => {
  const classEl = document.getElementById('class_applied');
  if (classEl) classEl.addEventListener('change', updateStreamDropdown);
});

// =============================================
// 7. PROGRESS BAR
// =============================================
const ALL_REQUIRED = [
  'student_name', 'dob', 'gender', 'aadhaar', 'class_applied',
  'father_name', 'mother_name', 'phone', 'email', 'address', 'city', 'state', 'pincode',
  'photo', 'aadhaar_front', 'aadhaar_back', 'declaration'
];

function updateProgress() {
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  if (!progressBar || !progressText) return;

  let filled = 0;
  ALL_REQUIRED.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.type === 'checkbox') { if (el.checked) filled++; }
    else if (el.type === 'file') { if (el.files && el.files.length > 0) filled++; }
    else if (el.value.trim() !== '') filled++;
  });
  const pct = Math.round((filled / ALL_REQUIRED.length) * 100);
  progressBar.style.width = pct + '%';
  progressText.textContent = pct + '%';
  updateStepIndicators(filled);
}

function updateStepIndicators(filled) {
  const thresholds = [0, 6, 13, 16];
  const steps = document.querySelectorAll('.step-item');
  steps.forEach((item, i) => {
    const circle = item.querySelector('.step-circle');
    if (!circle) return;
    if (filled >= thresholds[i + 1]) {
      circle.classList.add('done'); circle.classList.remove('active');
    } else if (filled >= thresholds[i]) {
      circle.classList.add('active'); circle.classList.remove('done');
    } else {
      circle.classList.remove('done', 'active');
    }
  });
}

ALL_REQUIRED.forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener('change', updateProgress);
});
document.querySelectorAll('.form-input:not(input[type=file])').forEach(el => {
  el.addEventListener('input', updateProgress);
});

// =============================================
// 7. FILE UPLOAD UX
// =============================================
const fileFields = [
  { id: 'photo', zone: 'zone_photo', label: 'name_photo' },
  { id: 'aadhaar_front', zone: 'zone_aadhaar_front', label: 'name_aadhaar_front' },
  { id: 'aadhaar_back', zone: 'zone_aadhaar_back', label: 'name_aadhaar_back' },
  { id: 'marksheet', zone: 'zone_marksheet', label: 'name_marksheet' }
];

const ALLOWED_TYPES = {
  photo: ['image/jpeg', 'image/png'],
  aadhaar_front: ['image/jpeg', 'image/png', 'application/pdf'],
  aadhaar_back: ['image/jpeg', 'image/png', 'application/pdf'],
  marksheet: ['image/jpeg', 'image/png', 'application/pdf']
};
const MAX_SIZE = 2 * 1024 * 1024; // 2 MB

fileFields.forEach(({ id, zone, label }) => {
  const input = document.getElementById(id);
  const zoneEl = document.getElementById(zone);
  const labelEl = document.getElementById(label);
  if (!input || !zoneEl || !labelEl) return;

  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    if (!ALLOWED_TYPES[id].includes(file.type)) {
      showError(id, 'Invalid file type.');
      input.value = ''; return;
    }
    if (file.size > MAX_SIZE) {
      showError(id, 'File exceeds 2 MB.'); input.value = ''; return;
    }
    clearError(id);
    // Preserve the sub-text but update filename
    const firstSpan = labelEl.querySelector('span');
    if (firstSpan) {
      firstSpan.textContent = '✔ ' + file.name;
    } else {
      labelEl.textContent = '✔ ' + file.name;
    }
    zoneEl.classList.add('uploaded');
    updateProgress();
  });

  // Drag & drop
  zoneEl.addEventListener('dragover', e => { e.preventDefault(); zoneEl.classList.add('ring-2', 'ring-indigo-500'); });
  zoneEl.addEventListener('dragleave', () => zoneEl.classList.remove('ring-2', 'ring-indigo-500'));
  zoneEl.addEventListener('drop', e => {
    e.preventDefault();
    zoneEl.classList.remove('ring-2', 'ring-indigo-500');
    if (e.dataTransfer.files.length) {
      input.files = e.dataTransfer.files;
      input.dispatchEvent(new Event('change'));
    }
  });
});

// =============================================
// 8. VALIDATION HELPERS
// =============================================
function showError(fieldId, msg) {
  const el = document.getElementById('err_' + fieldId);
  const input = document.getElementById(fieldId);
  if (el) el.textContent = msg;
  if (input) { input.classList.add('invalid'); input.classList.remove('valid'); }
}
function clearError(fieldId) {
  const el = document.getElementById('err_' + fieldId);
  const input = document.getElementById(fieldId);
  if (el) el.textContent = '';
  if (input) { input.classList.remove('invalid'); input.classList.add('valid'); }
}

function validateForm() {
  let valid = true;

  function req(id, errKey) {
    const el = document.getElementById(id);
    if (!el) return;
    const val = el.tagName === 'SELECT' ? el.value : el.value.trim();
    if (!val) { showError(id, t(errKey)); valid = false; }
    else clearError(id);
  }

  // Student
  req('student_name', 'err.name_req');
  const dob = document.getElementById('dob');
  if (!dob.value) { showError('dob', t('err.dob_req')); valid = false; }
  else if (new Date(dob.value) > new Date()) { showError('dob', t('err.dob_future')); valid = false; }
  else clearError('dob');
  req('gender', 'err.gender_req');
  const aadhaar = document.getElementById('aadhaar');
  if (!aadhaar.value.trim()) { showError('aadhaar', t('err.aadhaar_req')); valid = false; }
  else if (!/^\d{12}$/.test(aadhaar.value.trim())) { showError('aadhaar', t('err.aadhaar_fmt')); valid = false; }
  else clearError('aadhaar');
  req('class_applied', 'err.class_req');

  // Stream (only required for Class 9-12)
  const cls = (document.getElementById('class_applied')?.value || '');
  if (['Class 9', 'Class 10', 'Class 11', 'Class 12'].includes(cls)) {
    const streamEl = document.getElementById('stream');
    if (!streamEl || !streamEl.value) { showError('stream', t('err.stream_req')); valid = false; }
    else clearError('stream');
  }

  // Contact
  req('father_name', 'err.father_req');
  req('mother_name', 'err.mother_req');
  const phone = document.getElementById('phone');
  if (!phone.value.trim()) { showError('phone', t('err.phone_req')); valid = false; }
  else if (!/^[6-9]\d{9}$/.test(phone.value.trim())) { showError('phone', t('err.phone_fmt')); valid = false; }
  else clearError('phone');
  const email = document.getElementById('email');
  if (!email.value.trim()) { showError('email', t('err.email_req')); valid = false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) { showError('email', t('err.email_fmt')); valid = false; }
  else clearError('email');
  req('address', 'err.address_req');
  req('city', 'err.city_req');
  req('state', 'err.state_req');
  const pincode = document.getElementById('pincode');
  if (!pincode.value.trim()) { showError('pincode', t('err.pincode_req')); valid = false; }
  else if (!/^\d{6}$/.test(pincode.value.trim())) { showError('pincode', t('err.pincode_fmt')); valid = false; }
  else clearError('pincode');

  // Documents
  const photo = document.getElementById('photo');
  if (!photo.files || !photo.files.length) { showError('photo', t('err.photo_req')); valid = false; }
  else clearError('photo');
  const af = document.getElementById('aadhaar_front');
  if (!af.files || !af.files.length) { showError('aadhaar_front', t('err.af_req')); valid = false; }
  else clearError('aadhaar_front');
  const ab = document.getElementById('aadhaar_back');
  if (!ab.files || !ab.files.length) { showError('aadhaar_back', t('err.ab_req')); valid = false; }
  else clearError('aadhaar_back');

  // Declaration
  const decl = document.getElementById('declaration');
  const declErr = document.getElementById('err_declaration');
  if (!decl.checked) { if (declErr) declErr.textContent = t('err.decl_req'); valid = false; }
  else { if (declErr) declErr.textContent = ''; }

  return valid;
}

// Real-time blur validation
document.querySelectorAll('.form-input').forEach(el => {
  el.addEventListener('blur', () => {
    if (el.required && el.value.trim() === '') el.classList.add('invalid');
    else if (el.value.trim() !== '') { el.classList.remove('invalid'); el.classList.add('valid'); }
  });
});

// =============================================
// 9. FORM SUBMISSION (AJAX)
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  const admissionForm = document.getElementById('admissionForm');
  const submitBtn = document.getElementById('submitBtn');
  const submitText = document.getElementById('submitText');
  const loadingOverlay = document.getElementById('loadingOverlay');
  const successModal = document.getElementById('successModal');
  if (!admissionForm) return;

  admissionForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validateForm()) {
      const firstError = document.querySelector('.invalid, .error-msg:not(:empty)');
      if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    loadingOverlay.classList.remove('hidden'); loadingOverlay.classList.add('flex');
    submitBtn.disabled = true;
    if (submitText) submitText.textContent = currentLang === 'hi' ? 'जमा हो रहा है…' : 'Submitting…';

    try {
      const formData = new FormData(admissionForm);
      const data = {};

      // Separate file fields and other fields
      formData.forEach((value, key) => {
        if (!(value instanceof File)) {
          data[key] = value;
        }
      });

      // Handle Files — Convert to Base64 (to match existing DB logic)
      const filePromises = [];
      const filesToProcess = ['photo', 'aadhaar_front', 'aadhaar_back', 'marksheet'];

      for (const field of filesToProcess) {
        const fileInput = document.getElementById(field);
        if (fileInput && fileInput.files && fileInput.files[0]) {
          const file = fileInput.files[0];
          filePromises.push(new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
              data[field] = e.target.result.split(',')[1];
              data[field + '_mime'] = file.type;
              resolve();
            };
            reader.readAsDataURL(file);
          }));
        }
      }

      await Promise.all(filePromises);

      const response = await fetch(GOOGLE_SCRIPT_URL, {
        method: 'POST',
        mode: 'no-cors', // Important for GAS Web App
        cache: 'no-cache',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      // Note: 'no-cors' mode doesn't allow reading the response body, 
      // but the data will be sent successfully. 
      // For a better UX, we'll assume success if no error is thrown
      // or redirect to a generic success page if we can't get the ID.

      loadingOverlay.classList.add('hidden'); loadingOverlay.classList.remove('flex');
      submitBtn.disabled = false;
      if (submitText) submitText.textContent = t('btn.submit');

      localStorage.removeItem(AUTO_SAVE_KEY);
      // Since no-cors hides the ID, we redirect to confirmation with a 'success' flag
      window.location.href = 'confirmation.html?status=success';
    } catch (err) {
      loadingOverlay.classList.add('hidden'); loadingOverlay.classList.remove('flex');
      submitBtn.disabled = false;
      if (submitText) submitText.textContent = t('btn.submit');
      showToast('Error: ' + err.message, 'error');
    }
  });
});

// =============================================
// 10. TOAST NOTIFICATION
// =============================================
function showToast(msg, type = 'success') {
  const existing = document.getElementById('toast');
  if (existing) existing.remove();
  const toast = document.createElement('div');
  toast.id = 'toast';
  toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-xl text-white text-sm font-semibold flex items-center gap-2 transition-all duration-300';
  toast.style.background = type === 'success'
    ? 'linear-gradient(135deg,#10b981,#059669)'
    : 'linear-gradient(135deg,#ef4444,#dc2626)';
  toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> ${msg}`;
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
}

// =============================================
// 11. INIT
// =============================================
window.addEventListener('load', () => {
  updateProgress();
  const sections = document.querySelectorAll('.section-animate');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.style.animationPlayState = 'running'; });
  }, { threshold: 0.1 });
  sections.forEach(s => observer.observe(s));
});

window.printApplication = () => window.print();
