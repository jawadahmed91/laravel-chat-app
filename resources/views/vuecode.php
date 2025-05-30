async function getJwtToken() {
// Step 1: Try to check if we already have valid JWT token
const checkRes = await fetch('http://laravel-chat-app.test/api/auth/check', {
headers: {
'Authorization': `Bearer ${localStorage.getItem('chat_token')}`
}
});

if (checkRes.ok) {
const data = await checkRes.json();
console.log('✅ Already authenticated:', data.user);
return data.token; // Already logged in
}

// Step 2: If no valid token, try SSO from CodeIgniter
const ssoRes = await fetch('http://ci4-crm.test/api/sso/token', {
credentials: 'include' // Important: include cookies/session
});

if (ssoRes.ok) {
const { token } = await ssoRes.json();
localStorage.setItem('chat_token', token);
console.log('🔐 Logged in via SSO');
return token;
}

// Step 3: Fallback — show login modal
return new Promise((resolve) => {
showLoginModal((email, password) => {
loginWithCredentials(email, password).then(resolve);
});
});
}

async function loginWithCredentials(email, password) {
const res = await fetch('http://laravel-chat-app.test/api/auth/token', {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify({ email, password })
});

if (!res.ok) {
alert('Invalid credentials');
return null;
}

const data = await res.json();
localStorage.setItem('chat_token', data.token);
return data.token;
}
