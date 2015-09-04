package culinarydirectors.culinarydirectors;
import android.app.Activity;
import android.util.Log;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Sreenath on 9/2/2015.
 */
public class ActivityCallbacks {
    //constructor
    public ActivityCallbacks(){}
    //callbacks
    public void loginCallback(JSONObject data, Activity ac){
        /*
        String userJSON = "{\"uid\":1,\"name\":\"Sreenath Pillai\",\"email\":\"sreenath@flow-enterprises.com\",\"user_role\":0,\"orgId\":\"1\",\"orgName\":\"L.O.L\",\"img\":\""+this.TEST_PROFILE_IMG+
                "\",\"meal_plan_info\":\"3meals/wk\",\"dietary_restrictions\":\"fish and kale\""+"}";

        String menuJSON = "{\"menus\":[{\"displayDate\":\"WEDNESDAY // SEPTEMBER 9TH\",\"date\":\"09-02-2015\",\"name\":\"Asian Menu\",\"lunch_items\":[{\"id\":\"23\",\"name\":\"Chicken Stir-Fry\"},{\"id\":\"45\",\"name\":\"Beef Stir-Fry\"}]," +
                "\"dinner_items\":[{\"id\":\"45\",\"name\":\"Beef Stir-Fry\"},{\"id\":\"46\",\"name\":\"Orange Chicken\"},{\"id\":\"15\",\"name\":\"Chinese Chop Salad\"},{\"id\":\"7\",\"name\":\"Bubble Tea\"}]}," +
                "{\"displayDate\":\"THURSDAY // SEPTEMBER 10TH\",\"date\":\"09-03-2015\",\"name\":\"Asian Menu 2\",\"lunch_items\":[{\"id\":\"23\",\"name\":\"Too Much Stir-Fry\"},{\"id\":\"45\",\"name\":\"No More Stir-Fry\"}]," +
                "\"dinner_items\":[{\"id\":\"45\",\"name\":\"Fuck Stir-Fry\"},{\"id\":\"46\",\"name\":\"OK have sloppy joe\"},{\"id\":\"15\",\"name\":\"Tamarind-encrusted Haggis\"},{\"id\":\"7\",\"name\":\"Bubble Tea\"}]},"+
                "{\"displayDate\":\"FRIDAY // SEPTEMBER 11TH\",\"date\":\"09-03-2015\",\"name\":\"Asian Menu 3\",\"lunch_items\":[{\"id\":\"23\",\"name\":\"Squid Fry\"},{\"id\":\"45\",\"name\":\"Chopped Veggies\"},{\"id\":\"45\",\"name\":\"Ice Cream\"}]," +
                "\"dinner_items\":[{\"id\":\"45\",\"name\":\"Beef And Broccoli\"},{\"id\":\"46\",\"name\":\"Orange Pekoe Tea\"},{\"id\":\"15\",\"name\":\"Bok Choy\"},{\"id\":\"7\",\"name\":\"Bubble Tea\"}]}]}";

        String orgJSON = "{\"id\":1,\"name\":\"L.O.L\",\"email\":\"info@lolfrat.com\",\"org_info\":\"\",\"address\":\"123 1st st Madison, WI  53703\"}";

        String feedJSON = "{\"feed\":[{\"uid\":1,\"name\":\"Sreenath Pillai\",\"pic\":\"IMAGE_URL\",\"message\":\"I like batman\"},{\"uid\":2,\"name\":\"Not Pillai\",\"pic\":\"IMAGE_URL2\",\"message\":\"Who Cares.\"}]}";
*/
        Log.w("LOGIN_API_CALLBACK",data.toString());
        LoginActivity the_ac = (LoginActivity) ac;
        boolean loginSuccess = false;
        try {
            loginSuccess = data.getBoolean("login");
        } catch(JSONException e) {
            Log.w("json_exception", e.getMessage());
            loginSuccess = false;
        }
        if(loginSuccess) {
            the_ac.loginSuccess(data);
        }
        else {
            the_ac.loginFailure(data);
        }
    }

    public void registerCallback(JSONObject data, Activity ac) {
        Log.w("REGISTER_API_CALLBACK",data.toString());
        LoginActivity the_ac = (LoginActivity) ac;
        boolean registerSuccess = false;
        try {
            registerSuccess = data.getBoolean("success");
        } catch(JSONException e) {
            Log.w("json_exception", e.getMessage());
            registerSuccess = false;
        }
        if(registerSuccess) {
            the_ac.registerSuccess(data);
        }
        else {
            the_ac.registerFailure(data);
        }
    }
}
