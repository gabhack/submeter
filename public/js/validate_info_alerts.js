const validateEmail = (email) => {
  const regex = /^(([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)(\s*;\s*|\s*$))*$/;
  return regex.test(email.toLowerCase());
};

const validateForm = (form) => {
  let emails = form.getElementsByTagName("textarea");
  let errors = false;
  for (i = 0; i < emails.length; i++) {
    if (!validateEmail(emails[i].value)) {
      emails[i].classList.add("alert-danger");
      errors = true;
      continue;
    }
    emails[i].classList.remove("alert-danger");
  }
  if (!errors) {
    form.submit();
  }
};
