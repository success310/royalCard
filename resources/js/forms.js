"use strict";

/**
 * Processes the form validation response and updates the UI.
 * @param {string} response - The JSON string containing the form validation response.
 */
function processFormValidation(response) {
  let formResponse = JSON.parse(response);

  for (const key in formResponse) {
    // Add 'is-invalid' class to the field and attach event listeners
    let field = document.getElementById(key);
    field.classList.add('is-invalid');
    addFieldEventListeners(field);

    // Insert feedback message after the field
    insertFeedbackMessage(field, formResponse[key]);
  }
}

// Expose the processFormValidation function to the global scope
window.processFormValidation = processFormValidation;

/**
 * Adds event listeners to a form field for removing 'is-invalid' class.
 * @param {HTMLElement} field - The form field element.
 */
function addFieldEventListeners(field) {
  field.addEventListener('change', function () {
    this.classList.remove('is-invalid');
  });
  field.addEventListener('keydown', function () {
    this.classList.remove('is-invalid');
  });
}

/**
 * Inserts a feedback message after the form field.
 * @param {HTMLElement} field - The form field element.
 * @param {string} message - The feedback message to display.
 */
function insertFeedbackMessage(field, message) {
  let invalidFeedback = document.createElement('div');
  invalidFeedback.textContent = message;
  invalidFeedback.className = 'invalid-feedback';
  field.parentNode.insertBefore(invalidFeedback, field.nextSibling);
}

/**
 * Find the tab containing the first element with the 'is-invalid' class and click the tab to activate it.
 */
function openTabWithInvalidElement() {
  const firstInvalidElement = document.querySelector('.is-invalid, .is-invalid-label');
  let currentElement = firstInvalidElement;
  let parentTab;
  let activeTabValue;
  const xShowRegex = /activeTab\s*===\s*['"]([^'"]+)['"]/;

  // Traverse up the DOM tree to find the parent tab
  while (currentElement) {
    const xShowAttribute = currentElement.getAttribute('x-show');

    if (xShowAttribute) {
      const match = xShowAttribute.match(xShowRegex);

      if (match && match[1]) {
        parentTab = currentElement;
        activeTabValue = match[1];
        break;
      }
    }

    currentElement = currentElement.parentElement;
  }

  // If a parent tab is found
  if (parentTab) {
    // Find the tab element with the specified activeTabValue
    const tabElement = document.querySelector(`[x-on\\:click*="${activeTabValue}"]`);

    // If the tab element is found, trigger a click event on it
    if (tabElement) {
      tabElement.click();
    } else {
      console.error(`Tab with activeTab value '${activeTabValue}' not found`);
    }
  }
}

// Expose the openTabWithInvalidElement function to the global scope
window.openTabWithInvalidElement = openTabWithInvalidElement;


/**
 * Open tab.
 */
function openTab(activeTabValue) {
  // Find the tab element with the specified activeTabValue
  const tabElement = document.querySelector(`[x-on\\:click*="${activeTabValue}"]`);

  // If the tab element is found, trigger a click event on it
  if (tabElement) {
    tabElement.click();
  } else {
    console.error(`Tab with activeTab value '${activeTabValue}' not found`);
  }
}

// Expose the openTab function to the global scope
window.openTab = openTab;