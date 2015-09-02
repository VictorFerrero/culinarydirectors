package culinarydirectors.culinarydirectors;

import android.app.Activity;
import android.app.DialogFragment;
import android.app.Fragment;
import android.app.FragmentTransaction;
import android.content.Intent;
import android.graphics.Typeface;
import android.support.v4.app.FragmentActivity;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;

import org.json.JSONObject;


public class MainActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        initUI();
    }

    public void initUI(){
        //initialize Navigation
        Typeface font_awesome = Typefaces.get(this,"fontawesome.ttf");
        Button button = (Button)findViewById(R.id.btnMenu);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.btnFeed);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.btnProfile);
        button.setTypeface(font_awesome);
        //end initialize Navigation
        //populate UI from either webservice return vars; passed in from LoginActivity
        //  --either from onClick of login (first use) or auto-login from saved data (all other uses)
        Intent intent = getIntent();
        if(intent.getStringExtra(LoginActivity.MENUS) != null){
            //values from Login Activity
            String userJSON = intent.getStringExtra(LoginActivity.USER);
            String menuJSON = intent.getStringExtra(LoginActivity.MENUS);
            String orgJSON = intent.getStringExtra(LoginActivity.ORG);
        }
        else {
            //TODO:somehow they got here without logging in; log them out
        }

        //go to default tab - Calendar
        onCalendarClick(this.findViewById(R.id.btnMenu));
    }

    @Override
    public void onBackPressed() {
    }

    public void onCalendarClick(View dialog) {
        // User touched the dialog's negative button
        this.findViewById(R.id.calendar_view).setVisibility(View.VISIBLE);
        this.findViewById(R.id.profile_view).setVisibility(View.GONE);
        this.findViewById(R.id.feed_view).setVisibility(View.GONE);
    }
    public void onFeedClick(View dialog) {
        // User touched the dialog's negative button
        this.findViewById(R.id.calendar_view).setVisibility(View.GONE);
        this.findViewById(R.id.profile_view).setVisibility(View.GONE);
        this.findViewById(R.id.feed_view).setVisibility(View.VISIBLE);
    }
    public void onProfileClick(View dialog) {
        // User touched the dialog's negative button
        this.findViewById(R.id.calendar_view).setVisibility(View.GONE);
        this.findViewById(R.id.profile_view).setVisibility(View.VISIBLE);
        this.findViewById(R.id.feed_view).setVisibility(View.GONE);
    }
}
