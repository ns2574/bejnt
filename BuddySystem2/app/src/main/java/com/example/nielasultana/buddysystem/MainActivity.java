package com.example.nielasultana.buddysystem;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;


public class MainActivity extends Activity {

    private static final String TAG = "ActivityOP";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        String[] peopleInNeed = {"1", "2", "3", "4"};
        //ArrayList<String> personList = new ArrayList<String>();
        //personList.add("hellostrings");
//
//        Intent intentExtras = new Intent(MainActivity.this, MapsActivity.class);
//        intentExtras.putString("personID", person);
//        startActivity(intentExtras);

        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.list_layout, peopleInNeed);
        ListView helpyList = (ListView) findViewById(R.id.helpyList);
        helpyList.setAdapter(adapter);


    }


    public void openDialog(View view) {

        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        // Add the buttons
        builder.setPositiveButton("Non-Severe", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                // User clicked OK button

                // Testing
//                Intent intent = new Intent(MainActivity.this, MapsActivity.class);
//                startActivity(intent);
                String personid = "id";
                Intent intentBundle = new Intent(MainActivity.this, MapsActivity.class);
                Bundle bundle = new Bundle();
                bundle.putString(personid,"932939");
                intentBundle.putExtras(bundle);

                startActivity(intentBundle);

//                if(intentBundle.hasExtra(personid))
//                    Log.i(TAG, "Has String");
//                else
//                    Log.i(TAG, "No String");

            }
        });
        builder.setNeutralButton("Severe", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                // User cancelled the dialog
            }
        });

        // Create the AlertDialog
        AlertDialog dialog = builder.create();

        dialog.show();

    }

}
