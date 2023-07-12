"use strict";

import QRCode from "qrcode";

/**
 * Generates QR codes for elements with the "data-qr-url" attribute.
 * Customizes the QR codes based on additional data attributes provided.
 */
window.processQrCodes = function () {
  const elements = document.querySelectorAll("[data-qr-url]");

  elements.forEach(function (element) {
    const url = element.getAttribute("data-qr-url");
    const colorLight = element.getAttribute("data-qr-color-light") || "#fff";
    const colorDark = element.getAttribute("data-qr-color-dark") || "#000";
    const scale = element.getAttribute("data-qr-scale") || 4;

    const opts = {
      errorCorrectionLevel: "H",
      type: "image/png",
      quality: 0.3,
      margin: 3,
      small: true,
      scale: scale,
      color: {
        dark: colorDark,
        light: colorLight,
      },
    };

    // Generate the QR code using the QRCode.toDataURL method
    QRCode.toDataURL(
      url,
      opts,
      function (error, imageUrl) {
        if (error) console.error(error);
        else {
          // update the src attribute of the image element
          element.src = imageUrl;
        }
      }
    );

    console.log(url);
  });
};


window.processQrCodes();
