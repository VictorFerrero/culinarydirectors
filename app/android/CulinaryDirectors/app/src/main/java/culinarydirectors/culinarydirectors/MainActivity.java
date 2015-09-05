package culinarydirectors.culinarydirectors;

import android.app.Activity;
import android.app.DialogFragment;
import android.app.Fragment;
import android.app.FragmentTransaction;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Typeface;
import android.support.v4.app.FragmentActivity;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Base64;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.Date;


public class MainActivity extends Activity {

    //properties
    JSONObject userJSON;
    JSONObject menuJSON;
    int selected_menu = 0;
    JSONObject orgJSON;
    JSONObject feedJSON;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        try {
            initUI();
        } catch(Exception e){Log.w("Exception",e.getMessage()+e.getStackTrace());}
    }

    //TODO: handle JSONException
    public void initUI() throws JSONException{
        //swipe
        this.findViewById(R.id.calendar_view).setOnTouchListener(new OnSwipeTouchListener(this) {
            @Override
            public void onSwipeRight() {
                try {
                    showPrevDay();
                    Log.w("swipe event","swiped right");
                } catch (Exception e) {Log.w("Exception",e.getMessage()+e.getStackTrace());
                }
            }
            @Override
            public void onSwipeLeft() {
                try {
                    showNextDay();
                    Log.w("swipe event", "swiped left");
                } catch (Exception e) {Log.w("Exception",e.getMessage()+e.getStackTrace());
                }
            }
        });
        //initialize Navigation
        Typeface font_awesome = Typefaces.get(this,"fontawesome.ttf");
        Button button = (Button)findViewById(R.id.btnMenu);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.btnFeed);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.btnProfile);
        button.setTypeface(font_awesome);
        //end initialize Navigation
        //initialize thumbs
        button = (Button)findViewById(R.id.thumb_up_l1);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_up_l2);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_up_l3);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_up_d1);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_up_d2);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_up_d3);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_l1);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_l2);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_l3);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_d1);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_d2);
        button.setTypeface(font_awesome);
        button = (Button)findViewById(R.id.thumb_down_d3);
        button.setTypeface(font_awesome);
       /* TextView tv = (TextView) findViewById(R.id.arrowLeft);
        tv.setTypeface(font_awesome);
        tv = (TextView)findViewById(R.id.arrowRight);
        tv.setTypeface(font_awesome);*/
        //end initialize thumbs
        //set font-awesome
        TextView tv = (TextView) findViewById(R.id.fa_edit11);
        tv.setTypeface(font_awesome);
        tv = (TextView)findViewById(R.id.fa_edit12);
        tv.setTypeface(font_awesome);
        tv = (TextView)findViewById(R.id.fa_edit13);
        tv.setTypeface(font_awesome);
        tv = (TextView)findViewById(R.id.fa_edit14);
        tv.setTypeface(font_awesome);
        //populate UI from either webservice return vars; passed in from LoginActivity
        //  --either from onClick of login (first use) or auto-login from saved data (all other uses)
        Intent intent = getIntent();
        if(intent.getStringExtra(LoginActivity.MENUS) != null){
            //values from Login Activity
            userJSON = new JSONObject(intent.getStringExtra(LoginActivity.USER));
            menuJSON = new JSONObject(intent.getStringExtra(LoginActivity.MENUS));
            orgJSON = new JSONObject(intent.getStringExtra(LoginActivity.ORG));
            feedJSON = new JSONObject(intent.getStringExtra(LoginActivity.FEED));
        }
        else {
            //TODO:somehow they got here without logging in; log them out
        }

        //init tabs
        initCalendar();
        initFeed();
        initProfile();

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

    //private!
    private void initCalendar() {
        //date, name, lunch_items, dinner_items
        SimpleDateFormat sdfDate = new SimpleDateFormat("mm-dd-yyyy");
        Date now = new Date();
        String strDate = sdfDate.format(now);
        try {
            JSONArray menus = menuJSON.getJSONArray("menus");
            populateDate(0);
            /*for(int i=0; i<menus.length(); i++){
                JSONObject menu = menus.getJSONObject(i);
                if(menu.getString("date").equals(strDate)){
                    //select date
                    populateDate(menu);
                }
            }*/
        }catch(Exception e){
            Log.w("ERROR", e.getMessage() + e.getStackTrace());
        }
    }

    private void showNextDay(){
        try{
            populateDate(this.selected_menu+1);
        } catch(Exception e){Log.w("Exception",e.getMessage()+e.getStackTrace());}
    }

    private void showPrevDay(){
        try{
            populateDate(this.selected_menu-1);
        } catch(Exception e){Log.w("Exception",e.getMessage()+e.getStackTrace());}
    }

    private void initFeed(){
        try{
            //image
            byte[] decodedString = Base64.decode(userJSON.getString("img"), Base64.DEFAULT);
            Bitmap bitMap = BitmapFactory.decodeByteArray(decodedString, 0, decodedString.length);
            ImageView imageView = (ImageView) findViewById(R.id.msg_1_pic);
            imageView.setImageBitmap(bitMap);
            imageView = (ImageView) findViewById(R.id.msg_2_pic);
            imageView.setImageBitmap(bitMap);
            imageView = (ImageView) findViewById(R.id.msg_3_pic);
            imageView.setImageBitmap(bitMap);
        } catch(Exception e){Log.w("Exception",e.getMessage()+e.getStackTrace());}
    }

    private void initProfile(){
        try{
            //image
            byte[] decodedString = Base64.decode(userJSON.getString("img"), Base64.DEFAULT);
            Bitmap bitMap = BitmapFactory.decodeByteArray(decodedString, 0, decodedString.length);
            ImageView imageView = (ImageView) findViewById(R.id.profilePic);
            imageView.setImageBitmap(bitMap);
            //name, orgName
            EditText et = (EditText) findViewById(R.id.profileUsernameValue);
            et.setText(userJSON.getString("name"));
            et = (EditText) findViewById(R.id.profileOrgValue);
            et.setText(userJSON.getString("orgName"));
            //meal_plan_info and dietary_restrictions
            et = (EditText) findViewById(R.id.mealPlanValue);
            et.setText(userJSON.getString("meal_plan_info"));
            et = (EditText) findViewById(R.id.dietaryRestrictionsValue);
            et.setText(userJSON.getString("dietary_restrictions"));
        } catch(Exception e){Log.w("Exception",e.getMessage()+e.getStackTrace());}
    }

    private void populateDate(int index) throws JSONException{
        if(index <= menuJSON.getJSONArray("menus").length() - 1 && index >= 0){
            this.selected_menu = index;
            JSONArray data = menuJSON.getJSONArray("menus");
            JSONObject menu = data.getJSONObject(index);
            //set calendar title
            TextView tv = (TextView)this.findViewById(R.id.currentDate);
            tv.setText(menu.getString("displayDate"));
            //set menu items
            JSONArray lunch = menu.getJSONArray("lunch_items");
            JSONArray dinner = menu.getJSONArray("dinner_items");
            //TODO: change to loop and dynamic layout when pulling from service
            if(lunch.length() > 0 && lunch.get(0) != null){
                this.findViewById(R.id.calendar_l1).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_l1);
                tv.setText(lunch.getJSONObject(0).getString("name"));
            }
            if(lunch.length() > 1 && lunch.get(1) != null){
                this.findViewById(R.id.calendar_l2).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_l2);
                tv.setText(lunch.getJSONObject(1).getString("name"));
            } else { this.findViewById(R.id.calendar_l2).setVisibility(View.GONE); }
            if(lunch.length() > 2 && lunch.get(2) != null){
                this.findViewById(R.id.calendar_l3).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_l3);
                tv.setText(lunch.getJSONObject(2).getString("name"));
            } else { this.findViewById(R.id.calendar_l3).setVisibility(View.GONE); }
            //dinner
            if(dinner.get(0) != null){
                this.findViewById(R.id.calendar_d1).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_d1);
                tv.setText(dinner.getJSONObject(0).getString("name"));
            }
            if(dinner.get(1) != null){
                this.findViewById(R.id.calendar_d2).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_d2);
                tv.setText(dinner.getJSONObject(1).getString("name"));
            } else { this.findViewById(R.id.calendar_d2).setVisibility(View.GONE); }
            if(dinner.get(2) != null){
                this.findViewById(R.id.calendar_d3).setVisibility(View.VISIBLE);
                tv = (TextView)this.findViewById(R.id.menu_item_d3);
                tv.setText(dinner.getJSONObject(2).getString("name"));
            } else { this.findViewById(R.id.calendar_d3).setVisibility(View.GONE); }
        }
    }
}