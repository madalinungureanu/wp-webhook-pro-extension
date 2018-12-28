# Wp Webhooks Pro Extension
This extension plugin can be used as a template for extending the functionality of WP Webhooks Pro. 
It shows you how to create custom triggers and actions, as well as on how you can add multiple ones to the single plugin.

### How to setup an action
It basically is very simple
- You just copy the only line inside of the following function **add_webhook_actions_content** and 
customize the function name.
- After that you create the function you mentioned in the previous line and add an array as a return 
like in the function **action_delete_user_content** (You can copy that one as well).
- Now you can create another **case** inside of the switch statement in the function **add_webhook_actions** 
(You can copy the other case statemenet as well)
- For the case validation, you can use the **action** parameter you defined inside of the function similar to **action_delete_user_content**
- Then just change the function name wich is called inside of the switch statement and create it. This is the function that will
later recieve all of the external data. From there, you can do whatever you want to do with your data.

### How to setup a trigger
- To register a new trigger, copy the only line defined inside the following function: **add_webhook_triggers_content**
- After that you create the function you mentioned in the previous line and add an array as a return 
  like in the function **trigger_create_user_content** (You can copy that one as well). Inside of the array, you can also
  define a callback, which creates later on a custom hook wich you can use to register your demo data. You can check out 
  the following function for that **add_webhook_triggers**
 - Inside of the function **add_webhook_triggers** you can register your custom trigger that you want to call. This can 
 be a wordpress hook, a whole plugin or anything you desire to call. Also you can define there your demo callback that
 returns demo data.