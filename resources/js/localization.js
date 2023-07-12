"use strict";

fetch(window.location.href)
  .then((response) => {
    window.headerLocale = response.headers.get('X-App-Locale');
    window.headerLanguage = response.headers.get('X-App-Language');
    window.headerCurrency = response.headers.get('X-App-Currency');
    window.headerTimeZone = response.headers.get('X-App-TimeZone');

    window.formatDateTimes();
    window.formatDates();
    window.formatNumbers();
  })
  .catch((error) => {
    console.error('Error fetching headers:', error);
  });

/**
 * Formats a number according to the user's locale.
 * @param {number} number - The number to be formatted.
 * @return {string} The formatted number.
 */
window.appFormatNumber = function (number) {
  const locale = navigator.language;
  return (isNaN(number) || number == null || number == '') ? number : new Intl.NumberFormat(locale).format(parseFloat(number));
}

/**
 * Formats all elements with the class "format-number" according to the user's locale.
 */
window.formatNumbers = function () {
  const elements = document.getElementsByClassName("format-number");
  for (let element of elements) {
    const newContent = window.appFormatNumber(element.textContent);
    if (element.classList.contains("replace-container")) {
      // Create a text node with the new content
      const textNode = document.createTextNode(newContent);
      
      // Replace the original element with the new text node
      element.parentNode.replaceChild(textNode, element);
    } else {
      element.textContent = newContent;
    }
  }
}

/**
 * Formats a currency amount according to the user's locale.
 * @param {number} amount - The currency amount to be formatted.
 * @param {string} [currency='USD'] - The currency code (e.g., 'USD', 'EUR', 'JPY').
 * @return {string} The formatted currency amount.
 */
window.appFormatCurrency = function (amount, currency = 'USD') {
  const locale = navigator.language;
  return isNaN(amount)
    ? amount
    : new Intl.NumberFormat(locale, {
        style: "currency",
        currency: currency,
      }).format(amount);
}

/**
 * Formats a date string according to the user's locale and desired format.
 * 
 * @param {string} dateString - The date string to be formatted (in a format understood by JavaScript's Date object).
 * @param {string} dateFormat - The desired format for the date string. Expected values are 'md', 'lg', 'xl', or undefined.
 *                              'md': short weekday, 2-digit day, and long month.
 *                              'lg': short weekday, 2-digit day, long month, and numeric year.
 *                              'xl': long weekday, 2-digit day, long month, and numeric year.
 * @return {string} The formatted date string according to the user's locale and the specified dateFormat. 
 *                  If the dateString is not a valid date, the original dateString is returned.
 */
window.appFormatDate = function (dateString, dateFormat) {
  // Convert the dateString into a Date object
  const date = new Date(dateString);

  // Check if date is valid, if not return the original string
  if (isNaN(date)) {
    return dateString;
  }

  // Use the language from the header or fallback to the browser's language
  const locale = window.headerLanguage || navigator.language;

  // Default format options
  let formatOptions = { timeZone: window.headerTimeZone };

  // Set format options based on dateFormat
  switch (dateFormat) {
    case 'md':
      formatOptions = { 
        ...formatOptions,
        weekday: 'short', 
        day: '2-digit', 
        month: 'long',
      };
      break;
    case 'lg':
      formatOptions = { 
        ...formatOptions,
        weekday: 'short', 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric'
      };
      break;
    case 'xl':
      formatOptions = { 
        ...formatOptions,
        weekday: 'long', 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric'
      };
      break;
    default:
      // You can specify a default format option for any unexpected dateFormat values
      formatOptions = { 
        ...formatOptions,
        day: '2-digit', 
        month: 'short', 
        year: 'numeric'
      };
      break;
  }

  // Create a DateTimeFormat object with the provided locale and format options
  const dateTimeFormat = new Intl.DateTimeFormat(locale, formatOptions);

  // Return the formatted date
  return dateTimeFormat.format(date);
};

/**
 * Formats all elements with the class "format-date" according to the user's locale.
 * It fetches the date and format from data attributes of the element, and uses these to format the date.
 */
window.formatDates = function () {
  // Fetch all elements with the class "format-date"
  const elements = document.getElementsByClassName("format-date");

  // Iterate over each element
  for (const element of elements) {
    // Fetch the 'data-date' attribute, or fallback to the text content of the element
    const date = element.getAttribute('data-date') || element.textContent;

    // Fetch the 'data-date-format' attribute, or fallback to 'plain'
    const dateFormat = element.getAttribute('data-date-format') || 'plain';

    // If a date exists, format it according to the dateFormat and update the text content of the element
    if (date) {
      element.textContent = window.appFormatDate(date, dateFormat);
    }
  }
};

/**
 * Formats a date time according to the user's locale.
 * @param {string} dateTimeString - The date time string to be formatted.
 * @return {string} The formatted date time.
 */
window.appFormatDateTime = function (dateTimeString, local = false) {
  const date = new Date(dateTimeString);
  if (isNaN(date)) {
    return dateTimeString;
  }
  const locale = window.headerLanguage || navigator.language;
  const timeZone = (local) ? Intl.DateTimeFormat().resolvedOptions().timeZone : window.headerTimeZone;
  const dateTimeFormat = new Intl.DateTimeFormat(locale, {
    timeZone: timeZone,
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "numeric"
  });
  return dateTimeFormat.format(date);
}

/**
 * Formats all elements with the class "format-date-time" or "format-date-time-local" according to the user's locale.
 */
window.formatDateTimes = function () {
  const elementsDateTime = document.getElementsByClassName("format-date-time");
  for (let element of elementsDateTime) {
    const dateTime = element.getAttribute('data-date-time') || element.textContent;
    if (dateTime) {
      element.textContent = window.appFormatDateTime(dateTime);
    }
  }

  const elementsDateTimeLocal = document.getElementsByClassName("format-date-time-local");
  for (let element of elementsDateTimeLocal) {
    const dateTimeLocal = element.getAttribute('data-date-time-local') || element.textContent;
    if (dateTimeLocal) {
      element.textContent = window.appFormatDateTime(dateTimeLocal, true);
    }
  }
};

window.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
