# Predict The Event Demo
Simple Demo of an Event Guessing Pool - like a river breakup
Designed to work for the small screen - and large.
## Big Picture
A calendar of allowed guesses
## Selection Details
A detailed place to select a guess.
## Selection process
Deliberately omitted. 

### Third Party Collaboration
An example of how the API's on a third party (ie authorized ticket seller) could collaborate with the customized Event Guessing

### Deployment Notes
No CI/CD - copy files over and edit
Current code works in a dev environment configured for a icebreakup.localhost on an nginx locally.
Designed to go into a folder - doesn't need to be at root but you need to edit
config.php (only contains the URL of wherever you put it)
and purchaseMock/index has a config variable that needs to have correct paths

The there needs to be permission to write to the folders where the logs and jasnl files are created/updated:
api/interop
purchaseMock/api/signReceipt

Security Disclaimers: CORS and Rate Limited omitted for this example. 

The 3rd party call to our API would typically be called from their the 3rd party provider's server, 
not from the client html in this example. 
(a comment inserted in the purchaseMock APi to indicate appropriate place for call to our API )

Other approaches could be done - for example: the client HTML could do a very simple API "ping"
that would immediatly have your server make a secure and authenticated call to the 3rd party API 
to get the rest of the data so that the 3rd party has more control over the process. 

The refactored code shows a server to server example.
Note: The demo link does not do this since it's all on the same domain and my server blocks in API calls on same domain by default.  
A deployment of this code as-is would not be a realistic scenario - everything in purchaseMock would be used as an example for the coders at the 3rd party service provider - who would obviously be on a different domain. 