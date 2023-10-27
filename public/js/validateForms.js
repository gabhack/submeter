const formsToValidate = document.querySelectorAll("form")

const validationMethods = new Map([
  ["float", isValidFloat],
  ["email-list", isValidEmailList],
])

formsToValidate.forEach((form) => {
  const inputs = getInputs(form)
  if (isNull(inputs)) {
    return
  }
  form.addEventListener("submit", (e) => {
    let formIsValid = true

    inputs.forEach((input) => {
      console.log(input.parentElement)
      if (!isValidInput(input)) {
        input.classList.add("alert-danger")
        formIsValid = false
      } else {
        input.classList.remove("alert-danger")
      }
    })

    if (!formIsValid) {
      e.preventDefault()
    }
  })
})

function removeErrors(form) {}

function getInputs(form) {
  const formId = form.getAttribute("id")
  const insideFormInputs = Array.from(
    form.querySelectorAll("[data-form-validation]")
  )

  if (!isNull(formId)) {
    const outsideFormInputs = Array.from(
      document.querySelectorAll(`[form=${formId}][data-form-validation]`)
    )

    return insideFormInputs.concat(outsideFormInputs)
  }

  return insideFormInputs
}

function isValidInput(input) {
  const validationType = input.dataset.formValidation
  const validationMethod = validationMethods.get(validationType)
  const inputValue = input.value

  if (!isNull(validationMethod)) {
    return validationMethod(inputValue)
  }
}

function isValidFloat(float) {
  const regex = /^\-?\d+(\.\d+)?$/

  return regex.test(float)
}

function isValidEmailList(emailList) {
  const regex = /^(([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)(\s*;\s*|\s*$))*$/

  return regex.test(emailList.toLowerCase())
}

function isNull(variable) {
  return variable === undefined || variable === null
}

function isEmpty(variable) {
  return variable === ""
}
