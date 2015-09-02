package culinarydirectors.culinarydirectors;

import android.app.Activity;
import android.util.Log;

import org.json.JSONObject;

/**
 * Created by Sreenath on 9/2/2015.
 */
public class ActivityCallbacks {
    //constructor
    public ActivityCallbacks(){}
    //callbacks
    public void loginCallback(JSONObject data, Activity ac){
        Log.w("API_CALLBACK_REACHED",data.toString());
        LoginActivity the_ac = (LoginActivity) ac;
        the_ac.loginSuccess();
    }
}
