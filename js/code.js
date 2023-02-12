const urlBase = 'https://COP4331-23.com/LAMPAPI'
const extension = 'php'

let userId = 0
let firstName = ''
let lastName = ''

let contactIDs = []

/**
 * Login function
 * If invalid login, then outputs error to text box on screen
 * TODO: Hash passwords once everything is complete
 */
function doLogin () {
  userId = 0
  firstName = ''
  lastName = ''

  const login = document.getElementById('loginName').value
  const password = document.getElementById('loginPassword').value
  // const hash = md5(password)

  document.getElementById('loginResult').innerHTML = ''

  const tmp = { login, password }
  // const tmp = {
  //   login,
  //   password: hash
  // }
  const jsonPayload = JSON.stringify(tmp)

  const url = urlBase + '/Login.' + extension

  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        const jsonObject = JSON.parse(xhr.responseText)
        userId = jsonObject.id

        if (userId < 1) {
          document.getElementById('loginResult').innerHTML = 'User/Password combination incorrect'
          return
        }

        firstName = jsonObject.firstName
        lastName = jsonObject.lastName

        saveCookie()

        window.location.href = 'contacts.html'
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    document.getElementById('loginResult').innerHTML = err.message
  }
}

/**
 * Signs up new user
 */
function doSignup () {
  userId = 0
  firstName = ''
  lastName = ''

  const first = document.getElementById('firstName').value
  const last = document.getElementById('lastName').value
  const login = document.getElementById('signupUsername').value
  const password = document.getElementById('signupPassword').value
  // const hash = md5(password)

  document.getElementById('signupResult').innerHTML = ''

  // const tmp = { login, password }
  const tmp = {
    firstname: first,
    lastname: last,
    login,
    password
  }
  const jsonPayload = JSON.stringify(tmp)

  const url = urlBase + '/AddUser.' + extension

  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      // JSON object not ready yet
      if (this.readyState !== 4) {
        return
      }

      // Username conflict
      if (this.status === 409) {
        document.getElementById('signupResult').innerHTML = 'Username already taken'
        return
      }

      // TODO: Handle username conflict which is error 409
      if (this.readyState === 4 && this.status === 200) {
        const jsonObject = JSON.parse(xhr.responseText)
        userId = jsonObject.id

        firstName = jsonObject.firstName
        lastName = jsonObject.lastName

        saveCookie()

        window.location.href = 'contacts.html'
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    document.getElementById('signupResult').innerHTML = err.message
  }
}

function saveCookie () {
  const minutes = 20
  const date = new Date()
  date.setTime(date.getTime() + (minutes * 60 * 1000))
  document.cookie = 'firstName=' + firstName + ',lastName=' + lastName + ',userId=' + userId + ';expires=' + date.toGMTString()
}

function readCookie () {
  userId = -1
  const data = document.cookie
  const splits = data.split(',')
  for (let i = 0; i < splits.length; i++) {
    const thisOne = splits[i].trim()
    const tokens = thisOne.split('=')
    if (tokens[0] === 'firstName') {
      firstName = tokens[1]
    } else if (tokens[0] === 'lastName') {
      lastName = tokens[1]
    } else if (tokens[0] === 'userId') {
      userId = parseInt(tokens[1].trim())
    }
  }

  if (userId < 0) {
    window.location.href = 'index.html'
  } else {
    document.getElementById('userName').innerHTML = firstName + ' ' + lastName
    loadContacts()
  }
}

function doLogout () {
  userId = 0
  firstName = ''
  lastName = ''
  document.cookie = 'firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT'
  window.location.href = 'index.html'
}

function addContact () {
  const cna = document.getElementById('contact-name').value
  // const cln = document.getElementById('contact-last-name').value
  const fpn = document.getElementById('contact-pnumber').value
  const cem = document.getElementById('contact-email').value

  const tmp = {
    userid: userId,
    name: cna,
    phone: encodeURI(fpn),
    email: cem
  }

  const jsonPayload = JSON.stringify(tmp)

  console.log(tmp)

  const url = urlBase + '/AddContact.' + extension

  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        console.log(jsonPayload)
        console.log('New contact added')
        // loadContacts()
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    console.log(err.message)
  }
}

function editContact () {
  const editContact = document.getElementById('editContactText').value
  document.getElementById('contactEditResult').innerHTML = ''

  const tmp = { contact: editContact, userId }
  const jsonPayload = JSON.stringify(tmp)

  const url = urlBase + '/EditContact.' + extension

  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        document.getElementById('contactEditResult').innerHTML = 'Contact information has been updated'
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    document.getElementById('contactEditResult').innerHTML = err.message
  }
}

function editRow (i) {
  document.getElementById(`sb-${i}`).style.display = 'inline-block'
  document.getElementById(`eb-${i}`).style.display = 'none'

  const cna = document.getElementById(`cna-${i}`).innerText
  const cpn = document.getElementById(`cpn-${i}`).innerText
  const cem = document.getElementById(`cem-${i}`).innerText

  const cnaText = cna.innerText
  const cpnText = cpn.innerText
  const cemText = cem.innerText

  cna.innerHTML = `<input id='cna-e-${i}' type='text' value='${cnaText}'>`
  cpn.innerHTML = `<input id='cpn-e-${i}' type='text' value='${cpnText}'>`
  cem.innerHTML = `<input id='cem-e-${i}' type='text' value='${cemText}'>`
}

function saveRow (i) {
  document.getElementById(`sb-${i}`).style.display = 'none'
  document.getElementById(`eb-${i}`).style.display = 'inline-block'
}

function deleteRow (i) {
  const name = document.getElementById('cna-' + i).innerText
  const con = confirm(`Would you like to delete ${name} from your contacts?`)

  if (!con) {
    return
  }

  console.log(name)

  const tmp = {
    id: contactIDs[i]
  }

  const jsonPayload = JSON.stringify(tmp)

  const url = urlBase + '/DeleteContact.' + extension

  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        console.log('Deleted contact!')
        loadContacts()
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    console.log(err.message)
  }
}

/**
 * loadContacts()
 * Populates the table with contacts of user
 * Note: Does not do search, that is local!
 */
function loadContacts () {
  // Prepare JSON payload
  const tmp = {
    userId
  }

  const jsonPayload = JSON.stringify(tmp)
  const url = urlBase + '/SearchContacts.' + extension

  // Handle API result
  const xhr = new XMLHttpRequest()
  xhr.open('POST', url, true)
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
  try {
    xhr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        console.log('Loading contacts')
        console.log(xhr.responseText)
        const jsonObject = JSON.parse(xhr.responseText)
        console.log(jsonObject)

        let lContacts = ''
        for (let i = 0; i < jsonObject.results.length; i++) {
          contactIDs[i] = jsonObject.results[i].id
          lContacts += "<tr id='c-row-" + i + "'>"
          lContacts += "<td id='cna-" + i + "'>" + '<span>' + jsonObject.results[i].name + '</span></td>'
          lContacts += "<td id='cpn-" + i + "'>" + '<span>' + jsonObject.results[i].email + '</span></td>'
          lContacts += "<td id='cem-" + i + "'>" + '<span>' + jsonObject.results[i].phone + '</span></td>'

          // TODO: Add buttons into last column for edit/delete
          lContacts += `<td><button id='eb-${i}' onclick="editRow(${i})"><span class="material-symbols-rounded">edit</span><button id='sb-${i}' onclick="saveRow(${i})"><span class="material-symbols-rounded">save</span></button><button id='db-${i}' onclick="deleteRow(${i})"><span class="material-symbols-rounded">delete</span></button></td>`
          lContacts += '</tr>'
        }

        document.getElementById('contacts-table-body').innerHTML = lContacts
      }
    }
    xhr.send(jsonPayload)
  } catch (err) {
    console.log(err.message)
  }
}

function filterContacts () {
  // Filter through contact table for names
  const search = document.getElementById('contactSearch')
  const searchIn = search.value.toUpperCase()
  const table = document.getElementById('contacts')
  const tr = table.getElementsByTagName('tr')

  // Loop through each contact and see if it matches
  for (let i = 0; i < tr.length; i++) {
    const name = tr[i].getElementsByTagName('td')[0]

    if (name) {
      const nameText = name.textContent || name.innerText

      tr[i].style.display = 'none'
      if (nameText.toUpperCase().indexOf(searchIn) > -1) {
        tr[i].style.display = ''
      } else if (nameText.toUpperCase().indexOf(searchIn) > -1) {
        tr[i].style.display = ''
      }
    }
  }
}

// eslint-disable-next-line no-unused-vars
function toggleAddForm () {
  const addForm = document.getElementById('add-contact-form')
  const contactsTable = document.getElementById('contacts')

  if (addForm.style.display === 'none') {
    addForm.style.display = 'block'
    contactsTable.style.display = 'none'
  } else {
    addForm.style.display = 'none'
    contactsTable.style.display = 'flex'
  }
}
