const handleCategoryNameFocusOut = (event) => {
  /**
   * @type {HTMLInputElement}
   */
  const input = event.target;
  input.classList.replace("form-control", "form-control-plaintext");
  input.readOnly = true;

  // TODO: Update category name in database AJAX 
};

const handleCategoryNameFocusIn = (event) => {
  /**
   * @type {HTMLInputElement}
   */
  const input = event.target;
  input.classList.replace("form-control-plaintext", "form-control");
  input.focus();
  input.readOnly = false;
};
