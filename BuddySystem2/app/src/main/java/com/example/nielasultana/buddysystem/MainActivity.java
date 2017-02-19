package com.example.nielasultana.buddysystem;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
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

        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.list_layout, peopleInNeed);
        ListView helpyList = (ListView) findViewById(R.id.helpyList);
        helpyList.setAdapter(adapter);

        new CreateOngoingNotification().execute();

    }


    public void openDialog(View view) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        // Add the buttons
        builder.setPositiveButton("Non-Severe", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                // User clicked OK button

                // TODO: Testing
                Intent intent = new Intent(MainActivity.this, MapsActivity.class);
                startActivity(intent);
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

    private class CreateOngoingNotification extends AsyncTask<Void, Void, Void>{
        private NotificationManager manager;
        private Notification notification;

        @Override
        protected Void doInBackground(Void... urls) {
            manager = (NotificationManager) getSystemService(NOTIFICATION_SERVICE);
            Intent intent = new Intent(MainActivity.this, MainActivity.class);
            PendingIntent pendingIntent = PendingIntent.getActivity(MainActivity.this, 0, intent, PendingIntent.FLAG_UPDATE_CURRENT);

            notification = new Notification.Builder(MainActivity.this)
                    .setSmallIcon(R.mipmap.ic_launcher)
                    .setContentTitle("System Active")
                    .setContentText("Running")
                    .setOngoing(true)
                    .setContentIntent(pendingIntent)
                    .build();

            return null;
        }

        @Override
        protected void onPostExecute(Void result) {
            manager.notify(0, notification);
        }
    }

}
