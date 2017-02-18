package com.example.nielasultana.buddysystem;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;


public class MainActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        String[] peopleInNeed = {"1", "2", "3", "4"};

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
