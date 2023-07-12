/**
 * Sets up multiple instances of an image upload component on a single page.
 * Each component allows users to upload an image and remove it using buttons.
 * Uses the DOMContentLoaded event to initialize event listeners for the components.
 *
 * Component structure:
 * - A label element with the class ".dropzone-label" wraps the component.
 * - An input element with the class ".dropzone-file" is used for file selection.
 * - An img element with the class ".image-preview" displays the uploaded image.
 * - A div element with the class ".image-wrapper" wraps the image-preview element.
 * - A button element with the class ".remove-image" is used to remove the uploaded image.
 * - A div element with the class ".upload-text" displays the upload instructions.
 */

document.addEventListener("DOMContentLoaded", () => {
    // Select all elements with the .dropzone-label class
    const dropzoneLabels = document.querySelectorAll(".dropzone-label");

    // Iterate over each .dropzone-label element
    dropzoneLabels.forEach((label) => {
        // Check if initial image is deleted
        let hasImage = false;

        // Get the parent container of the label element
        const container = label.parentElement;

        // Get the initial height of the .dropzone-label element
        const labelHeight = label.clientHeight;

        // Query child elements within the .dropzone-label element
        const inputElement = label.querySelector(".dropzone-file");
        const imagePreviewElement = label.querySelector(".image-preview");
        const imageWrapperElement = label.querySelector(".image-wrapper");
        const removeImageElement = container.querySelector(".remove-image");
        const uploadTextElement = label.querySelector(".upload-text");
        const imageChangedInputElement = label.querySelector(".image-changed");
        const imageDeletedInputElement = label.querySelector(".image-deleted");

        // Check if the image src is not empty or equal to "#"
        if (imagePreviewElement.src && imagePreviewElement.src !== "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=") {
            imageWrapperElement.classList.remove("hidden");
            removeImageElement.classList.remove("hidden");
            uploadTextElement.classList.add("hidden");
            hasImage = true;
        }

        // Add event listener for the input element's change event
        inputElement.addEventListener("change", (event) => {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function (event) {
                // Update the image preview with the uploaded image
                imagePreviewElement.src = event.target.result;
                imageWrapperElement.classList.remove("hidden");
                removeImageElement.classList.remove("hidden");
                uploadTextElement.classList.add("hidden");
            };
            reader.readAsDataURL(file);

            // Set the image has changed input value to "1" when an image is selected
            imageChangedInputElement.value = "1";

            // Set the image deleted input value to an empty string
            imageDeletedInputElement.value = "";
        });

        // Add event listener for the image preview element's load event
        imagePreviewElement.addEventListener("load", () => {
            // Set the height of the .dropzone-label element to match the image height
            label.style.height = `${imagePreviewElement.clientHeight}px`;
        });

        // Add event listener for the remove image button's click event
        removeImageElement.addEventListener("click", () => {
            // Reset the image preview and input element
            imagePreviewElement.src = "";
            imageWrapperElement.classList.add("hidden");
            removeImageElement.classList.add("hidden");
            uploadTextElement.classList.remove("hidden");
            inputElement.value = "";

            // Reset the height of the .dropzone-label element to its initial height
            if (labelHeight > 0) label.style.height = `${labelHeight}px`;

            // Reset the image has changed input value to an empty string when an image is removed
            imageChangedInputElement.value = "";

            // Set the image deleted input value to "1" to indicate that the image has to be deleted 
            if (hasImage) imageDeletedInputElement.value = "1";
        });
    });
});

/**
 * Set the height of all elements with the .dropzone-label class to match
 * the height of their respective .image-preview child element.
 * 
 * This function is intended to be used to adjust the height of the .dropzone-label
 * elements after an image has been uploaded.
 */
function setImageUploadHeight() {
    // Set a small delay to ensure the image has been rendered before adjusting heights
    setTimeout(() => {
      // Select all elements with the .dropzone-label class
      const dropzoneLabels = document.querySelectorAll(".dropzone-label");
  
      // Iterate over each .dropzone-label element
      dropzoneLabels.forEach((label) => {
        // Query child elements within the .dropzone-label element
        const imagePreviewElement = label.querySelector(".image-preview");
  
        // Check if the image src is not empty or equal to "#"
        if (imagePreviewElement.src && imagePreviewElement.src !== "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=") {
            // Set the height of the .dropzone-label element to match the image height
            if (imagePreviewElement.clientHeight > 0) label.style.height = `${imagePreviewElement.clientHeight}px`;
        }
      });
    }, 2);
  }
  
  // Assign the function to the global window object
  window.appSetImageUploadHeight = setImageUploadHeight;