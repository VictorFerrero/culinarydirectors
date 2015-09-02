package culinarydirectors.culinarydirectors;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;

import java.io.Console;

/**
 * Created by Sreenath on 8/31/2015.
 */
public class LoginActivity extends Activity {

    public final static String USER = "com.culinarydirectors.culinarydirectors.USER";
    public final static String MENUS = "com.culinarydirectors.culinarydirectors.MENUS";
    public final static String ORG = "com.culinarydirectors.culinarydirectors.ORG";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.dialog_signin);
    }
    public void onLoginClick(View dialog) {
        // User touched the dialog's positive button
        Log.w("login", "login clicked");
        Intent intent = new Intent(this, MainActivity.class);
        //TODO: use threadpool and webservice calls instead of static json.
        //TODO: load USER and MENUS from a route that returns an array with
        // "USER"=>user model function result of login,
        // "MENUS"=>list of menus for given user groupby date
        // "ORG" => org for user
        String userJSON = "{\"uid\":1,\"name\":\"Sreenath Pillai\",\"email\":\"sreenath@flow-enterprises.com\",\"user_role\":0,\"orgId\":\"1\"}";
        intent.putExtra(USER, userJSON);
        String menuJSON = "[{\"date\":\"9-1-2015\",\"name\":\"Asian Menu\",\"lunch_items\":[{\"id\":\"23\",\"name\":\"Chicken Stir-Fry\"},{\"id\":\"45\",\"name\":\"Beef Stir-Fry\"}]," +
                "\"dinner_items\":[{\"id\":\"45\",\"name\":\"Beef Stir-Fry\"},{\"id\":\"46\",\"name\":\"Orange Chicken\"},{\"id\":\"15\",\"name\":\"Chinese Chop Salad\"},{\"id\":\"7\",\"name\":\"Bubble Tea\"}]}]";
        intent.putExtra(MENUS, menuJSON);
        String orgJSON = "{\"id\":1,\"name\":\"EL OH EL\",\"email\":\"info@lolfrat.com\",\"org_info\":\"\",\"address\":\"123 1st st Madison, WI  53703\"}";
        intent.putExtra(ORG, orgJSON);
    }

    @Override
    public void onBackPressed() {
    }

    public void onSignupClick(View dialog) {
        // User touched the dialog's negative button

    }

    public void onRegisterClick(View dialog) {
        // User touched the dialog's negative button

    }

    public void onForgotClick(View dialog) {
        // User touched the dialog's negative button

    }

    public void onPWResetClick(View dialog) {
        // User touched the dialog's negative button

    }
}
