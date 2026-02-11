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
