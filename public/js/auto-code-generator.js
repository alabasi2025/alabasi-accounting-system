/**
 * مولد الرموز التلقائي
 * يقوم بتوليد رمز تلقائي بناءً على الاسم المدخل
 */

/**
 * تحويل النص العربي إلى رمز لاتيني
 */
function arabicToCode(text) {
    const arabicToLatin = {
        'ا': 'A', 'أ': 'A', 'إ': 'A', 'آ': 'A',
        'ب': 'B',
        'ت': 'T', 'ث': 'TH',
        'ج': 'J',
        'ح': 'H', 'خ': 'KH',
        'د': 'D', 'ذ': 'DH',
        'ر': 'R', 'ز': 'Z',
        'س': 'S', 'ش': 'SH',
        'ص': 'S', 'ض': 'D',
        'ط': 'T', 'ظ': 'Z',
        'ع': 'A', 'غ': 'GH',
        'ف': 'F',
        'ق': 'Q',
        'ك': 'K',
        'ل': 'L',
        'م': 'M',
        'ن': 'N',
        'ه': 'H',
        'و': 'W',
        'ي': 'Y', 'ى': 'Y',
        'ة': 'H',
        ' ': '-',
        'ال': ''
    };
    
    let code = '';
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        if (arabicToLatin[char]) {
            code += arabicToLatin[char];
        } else if (/[A-Za-z0-9]/.test(char)) {
            code += char.toUpperCase();
        }
    }
    
    return code;
}

/**
 * توليد رمز من الاسم
 * @param {string} name - الاسم المدخل
 * @param {string} prefix - البادئة (مثل: BR, CO, UN)
 * @param {number} maxLength - الحد الأقصى لطول الرمز (افتراضياً 10)
 * @returns {string} الرمز المولد
 */
function generateCode(name, prefix = '', maxLength = 10) {
    if (!name || name.trim() === '') {
        return '';
    }
    
    // إزالة المسافات الزائدة
    name = name.trim();
    
    // تحويل النص إلى رمز
    let code = arabicToCode(name);
    
    // إضافة البادئة
    if (prefix) {
        code = prefix + '-' + code;
    }
    
    // تحديد الطول
    if (code.length > maxLength) {
        code = code.substring(0, maxLength);
    }
    
    // إزالة الشرطات المتكررة
    code = code.replace(/-+/g, '-');
    
    // إزالة الشرطة من البداية والنهاية
    code = code.replace(/^-|-$/g, '');
    
    return code;
}

/**
 * ربط حقل الاسم بحقل الرمز
 * @param {string} nameFieldId - معرف حقل الاسم
 * @param {string} codeFieldId - معرف حقل الرمز
 * @param {string} prefix - البادئة
 * @param {number} maxLength - الحد الأقصى للطول
 */
function linkNameToCode(nameFieldId, codeFieldId, prefix = '', maxLength = 10) {
    const nameField = document.getElementById(nameFieldId);
    const codeField = document.getElementById(codeFieldId);
    
    console.log('linkNameToCode called:', nameFieldId, codeFieldId, prefix, maxLength);
    console.log('nameField:', nameField);
    console.log('codeField:', codeField);
    
    if (!nameField || !codeField) {
        console.error('Name field or code field not found');
        return;
    }
    
    console.log('Fields found successfully');
    
    // متغير لتتبع ما إذا كان المستخدم عدل الرمز يدوياً
    let manuallyEdited = false;
    
    // عند تغيير حقل الاسم
    nameField.addEventListener('input', function() {
        console.log('Name field input event triggered:', this.value);
        // إذا لم يتم التعديل يدوياً، نولد الرمز تلقائياً
        if (!manuallyEdited) {
            const generatedCode = generateCode(this.value, prefix, maxLength);
            console.log('Generated code:', generatedCode);
            codeField.value = generatedCode;
        } else {
            console.log('Skipped - manually edited');
        }
    });
    
    // عند التعديل اليدوي على حقل الرمز
    codeField.addEventListener('input', function() {
        manuallyEdited = true;
    });
    
    // إذا تم مسح حقل الرمز بالكامل، نعيد التوليد التلقائي
    codeField.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            manuallyEdited = false;
            const generatedCode = generateCode(nameField.value, prefix, maxLength);
            this.value = generatedCode;
        }
    });
}
