// The Auth0 client, initialized in configureClient()
let auth0 = null;

/**
 * Retrieves the auth configuration from the server
 */
const fetchAuthConfig = () => fetch("/auth_config.json");

/**
 * Initializes the Auth0 client
 */
const configureClient = async () => {
  const response = await fetchAuthConfig();
  const config = await response.json();

  auth0 = await createAuth0Client({
    domain: config.domain,
    client_id: config.clientId
  });
};

// Will run when page finishes loading
window.onload = async () => {
  await configureClient();

  const isAuthenticated = await auth0.isAuthenticated();

  if (isAuthenticated) {
    console.log("> User is authenticated");
    //redirect to authentication page - user will be authenticated automatically
    document.location.href = '/login.php' + document.location.search
    return;
  }

  console.log("> User not authenticated");
};