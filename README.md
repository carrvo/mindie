# MIndie

Built upon [SelfAuth](https://github.com/Inklings-io/selfauth) + [MinToken](https://github.com/Zegnat/php-mintoken.git)

A minimal self-hosted [IndieAuth](https://indieweb.org/IndieAuth) solution for the home.

This can be used completely isolated from the internet (with TLS warnings) in a homenet or, with the additional purchase of a domain (not included) and configuring [Let's Encrypt](https://letsencrypt.org/) (not documented here), it can be used on the public internet.

## Sub-Projects

- [MIndie-IdP](https://github.com/carrvo/mindie-idp)
- [MIndie-Client](https://github.com/carrvo/mindie-client)
- [MIndie-Profile](https://github.com/carrvo/mindie-profile)

### Setup

Follow the setup of each project and they can be configured under the same server/`VirtualHost`. Doing so is a quick way to get you a self-hosted test user and test webpage that can be run entirely `localhost`.

### Password Reset

An additional [MIndie-Client](https://github.com/carrvo/mindie-client) is provided to reset the password for [MIndie-IdP](https://github.com/carrvo/mindie-idp).

Endpoint: https://example.com/selfauth/password/reset

To setup this extra endpoint (after MIndie-Client and MIndie-IdP are setup):
1. Clone to `/usr/local/src/`
1. Run `as-a-client.bash`
1. Add configuration to your Apache HTTPd configuration
    ```
    Include /usr/local/src/mindie/password-reset.conf
    ```

## Welcome To IndieAuth

I you are like me you were probably scratching your head over two seemly simple things:
- how do the pieces of IndieAuth fit together?
- where is an install package to have a self-hosted test user and test webpage?

For the latter question: there isn't. Or wasn't. More specifically, there are lots of helpful blogs and tools for *creating your own solution* after reading and understanding the [spec](https://indieauth.spec.indieweb.org/), but are not a solution in of themselves. MIndie is meant to be a *minimum IndieAuth solution* that you can self-host. You can use the pieces together or with other implementations ([IndieAuth.com](https://indieauth.com/) has information about skipping a self-hosted IdP and just using your existing social accounts).

For the former question: well...read on.

### IndieAuth Background

IndieAuth is built upon [OAuth2.0](https://www.oauth.com/) ([Auth0.com](https://auth0.com/docs)) with one very desirable modification: you can use your own domain (or a generic user profile) to login to an enabled service! Furthermore, clients are also identified by their URL/URI so developers do not have to explicitly support your underlying IdP! Super exciting.

What do you need to take advantage of such a nifty login? Well...

### IndieAuth Pieces

For any given IndieAuth login, there are 4 parties involved. Yes, 4.
- user agent (this does all of the physical actions for you and is usually your browser)
- user profile (this is your personal public webpage!)
- client service (this is the service you are trying to login to and use, this can be a webpage or a web resource)
- IdP (this is what controls your identity and what you must Authenticate with before accessing a service)

Each of these parties can be separate software running/hosted on separate physical or virtual machines (hence "parties").

Each of these pieces, except the *user agent*, is available as a sub-project with more information on how to configure their side of the story.

### IndieAuth Flow (with Metadata Discovery)

1. The *user agent* **requests** the *client service* **login** page and displays it to you.
1. The *user agent* **requests** the *client service* to perform a login with **your supplied URL**.
1. The *client service* **requests** your *user profile* to **discover** the IdP metadata endpoint.
1. The *client service* **requests** your *IdP* metadata endpoint to **discover** the IdP URL.
1. The *client service* **responds** to the *user agent* with the IdP URL.
1. The *user agent* **requests** the *IdP* to **Authenticate and Authorize**.
1. The *IdP*, upon valid credentials, **responds** to the *user agent* with an **authorization code**.
1. The *user agent* **requests (including the authorization code)** the *client service* to complete the login.
1. The *client service* **requests (including the authorization code)** the *IdP* to validate the login.
1. The *IdP*, upon valid authorization code, **responds** to the *client service* with an **access token**.
1. The *client service* **responds (including a cookie with the access token)** to the *user agent* with a login success.
1. The *user agent* **requests (including the cookie with the access token)** the *client service* **webpage or resource**.
1. The *client service* **requests (including the access token)** the *IdP* for token information (called introspection).
1. The *IdP*, upon valid access token, **responds** to the *client service* with an **identity token**.
1. The *client service*, upon valid Authorization, **responds** to the *user agent* with the appropriate **webpage or resource**.

TODO: picture illustrating the flow
TODO: GIF illustrating the flow

Note that for a non-browser agent (including a client-side script), it would return the **access token** directly, instead of inside a cookie; and then the agent would have to include the `Authorize: Bearer <access token>` header instead of sending the **access token** inside a cookie.

### Wait, There are Multiple Discovery Mechanisms for Your Profile?

#### Authorization Endpoint *DEPRECATED*

A special `<link>` element is searched for to discover your Authorization endpoint. Optionally a second `<link>` endpoint is searched for if you want to support tokens (and you probably do, these contain permissions called "scopes").
```html
<link rel="authorization_endpoint" href="https://example.com/selfauth/index.php" />
<link rel="token_endpoint" href="https://example.com/selfauth/token.php" />
```

#### RelMeAuth

Various link-like elements are searched for to discover your online social identities. This comes with the added benefit of potentially being human-readable and clickable!
```html
<link rel="me" href="https://github.com/myuser" />
<a rel="me" href="https://github.com/myuser">My GitHub</a>
```

This is actually *not* IndieAuth, but its own [spec](http://microformats.org/wiki/RelMeAuth)
with the overlap of using your own user profile to sign-in.
However, [IndieAuth.com](https://indieauth.com/) provides a way to bridge between them.

#### Metadata Endpoint

A special `<link>` element is searched for to discover a metadata endpoint. This endpoint is then queried to discover all the additional information that helps to protect your identity.
```html
<link rel="indieauth-metadata" href="https://example.com/.well-known/oauth-authorization-server" />
```

### Conclusion

The answer to "how do the pieces of IndieAuth fit together?" has 4 pieces and a whole lot of connections behind the scenes. Hopefully this gives a more concise and comprehensive explanation to get you started with IndieAuth.

## License

Copyright 2024 by carrvo

### Licenses for Sub-Projects

- [MIndie-IdP](https://github.com/carrvo/mindie-idp) - Copyright 2024 by carrvo. Available under the MIT license.
- [MIndie-Client](https://github.com/carrvo/mindie-client) - Copyright 2024 by carrvo. Available under the MIT license.
- [MIndie-Profile](https://github.com/carrvo/mindie-profile) - CC0

