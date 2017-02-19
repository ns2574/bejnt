package com.example.nielasultana.buddysystem;
import android.Manifest;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Application;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;

import com.google.android.gms.maps.model.LatLng;

import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;

import java.net.HttpURLConnection;
import java.util.ArrayList;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

public class MainActivity extends Activity {
    private static final String URL_UPDATE = "http://52.37.120.183/hacknyu17/update.php";
    private static final String TAG = "ActivityOP";
    private static final int LOCATION_REQUEST_CODE = 2;
    private static final int UPDATE_INTERVAL = 5000;
    private Timer timer;
    private double longitude;
    private double latitude;
    private String phoneNumber;
//    public int login = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

//        TelephonyManager tMgr = (TelephonyManager)this.getSystemService(Context.TELEPHONY_SERVICE);
//        phoneNumber = tMgr.getLine1Number();
//
//        URL url;
//        HttpURLConnection client = null;
//
//        try {
//            url = new URL(URL_ADDUSER);
//            client = (HttpURLConnection) url.openConnection();
//            client.setRequestMethod("POST");
//            client.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
//            client.setRequestProperty("charset", "utf-8");
//            client.setDoInput(true);
//            client.setDoOutput(true);
//
//            String input = String.format("telephone=%s&latitude=%s&longitude=%s", phoneNumber, latitude, longitude);
//
//            OutputStream out = new BufferedOutputStream(client.getOutputStream());
//            out.write(input.getBytes());
//            out.flush();
//            out.close();
//
//        } catch (IOException e) {
//            Log.d("HTTPUpdate", e.getMessage());
//        }
//
//        try {
//            BufferedReader in = new BufferedReader(new InputStreamReader(client.getInputStream()));
//            String line = in.readLine();
//
//            if (line.startsWith("{C")) {
//
//            } else {
//
//            }
//
//        } catch (IOException e) {
//            Log.d("HTTPUpdate", e.getMessage());
//        }

//
//
// create a urlconnection object
//            HttpURLConnection con = (HttpURLConnection) url.openConnection();
//
//            con.setRequestMethod("POST");
//            con.setRequestProperty("USER-AGENT",phoneNumber);
//            con.setDoInput(true);
//
//            int responseCode = con.getResponseCode();
//            String output = "Request URL " + url;
//            output += System.getProperty("line.separator");
//            output += System.getProperty("line.seperator")+ responseCode;
//
//            // wrap the urlconnection in a bufferedreader
//            BufferedReader br = new BufferedReader(new InputStreamReader(con.getInputStream()));
//            String line;
//            StringBuilder responseOutput = new StringBuilder();
//
//            // read from the urlconnection via the bufferedreader
//            while ((line = br.readLine()) != null)
//            {
//                content.append(line + "\n");
//            }
//            br.close();
//
//            output += System.getProperty("line.seperator");
//
//        } catch (MalformedURLException e) {
//            e.printStackTrace();
//        } catch(Exception e)
//        {
//            e.printStackTrace();
//        }

        updateTimer();
//        String[] personList = {"Person A needs help"};
//        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.list_layout, personList);
//        ListView helpyList = (ListView) findViewById(R.id.helpyList);
//        helpyList.setAdapter(adapter);

    }

    public class ApplicationAdapter extends ArrayAdapter<String> {
        private List<String> items;
        ListView listview;

        public ApplicationAdapter(Context context, List<String> items) {
            super(context, R.layout.list_layout, items);
            this.items = items;

        }

        @Override
        public int getCount() {
            return items.size();
        }


        @Override
        public View getView(int position, View convertView, ViewGroup parent) {
            View v = convertView;

            if (v == null) {
                LayoutInflater li = LayoutInflater.from(getContext());
                v = li.inflate(R.layout.list_layout, null);
            }

            String s = items.get(position);

            Button button = (Button) v;
            button.setText(s);

            button.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {

                }
            });

            return v;
        }

    }



//    helpyList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
//
//        @Override
//        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
//
//            //get position index of item here.
//
//            String indexid = String.valueOf(position);
//
//            //and do whatever afterwards.
//
//
//        });
//
//    }

    public void listLayoutSetup(){
        ArrayList<String> timeStamp= new ArrayList<String>();
        timeStamp.add("666");
        ArrayList<String> hashedNumbs= new ArrayList<String>();
        hashedNumbs.add("000000");
        ArrayList<String> showedText= new ArrayList<String>();

        for (int i = 0; i < hashedNumbs.size(); i++){
            showedText.add(timeStamp.get(i)+ "Person in need of help");

        }
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.list_layout, showedText);
        ListView helpyList = (ListView) findViewById(R.id.helpyList);
        helpyList.setAdapter(adapter);
        String personid = "id";
        Intent intentBundle = new Intent(MainActivity.this, MapsActivity.class);
        Bundle bundle = new Bundle();
        bundle.putString(personid,"2938498349");
        intentBundle.putExtras(bundle);

        startActivity(intentBundle);
    }

    private void updateTimer() {
        final Handler handler = new Handler();
        timer = new Timer();
        timer.schedule(new TimerTask() {
            @Override
            public void run() {
                handler.post(new Runnable() {
                    @Override
                    public void run() {
                        getLocation();
                        listLayoutSetup();
                        new GetUpdate(phoneNumber, String.valueOf(latitude), String.valueOf(longitude)).execute(false);
                    }
                });
            }
        }, 0, UPDATE_INTERVAL);
    }

    public void openDialog(View view) {

        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        // Add the buttons
        builder.setPositiveButton("Non-Severe", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                // User clicked Non-Severe button

//                String personid = "id";
//                Intent intentBundle = new Intent(MainActivity.this, MapsActivity.class);
//                Bundle bundle = new Bundle();
//                bundle.putString(personid,"932939");
//                intentBundle.putExtras(bundle);
//
//                startActivity(intentBundle);
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

    private void getLocation(){
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_DENIED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, LOCATION_REQUEST_CODE);
        } else {

            LocationManager locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
            locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 2000, 10, new LocationListener() {
                @Override
                public void onLocationChanged(Location location) {
                    longitude = location.getLongitude();
                    latitude = location.getLatitude();
                }

                @Override public void onStatusChanged(String provider, int status, Bundle extras) {}
                @Override public void onProviderEnabled(String provider) {}
                @Override public void onProviderDisabled(String provider) {}
            });

            Location location = locationManager.getLastKnownLocation(LocationManager.PASSIVE_PROVIDER);
            longitude = location.getLongitude();
            latitude = location.getLatitude();
        }
    }

    private class GetUpdate extends AsyncTask<Boolean, Void, Boolean> {

        private LatLng location;
        private String tel;
        private String lat;
        private String lon;

        public GetUpdate(String tel, String lat, String lon) {
            this.tel = tel;
            this.lat = lat;
            this.lon = lon;
        }

        @Override
        protected Boolean doInBackground(Boolean... booleans) {
            URL url;
            HttpURLConnection client = null;

            try {
                url = new URL(URL_UPDATE);
                client = (HttpURLConnection) url.openConnection();
                client.setRequestMethod("POST");
                client.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
                client.setRequestProperty("charset", "utf-8");
                client.setDoInput(true);
                client.setDoOutput(true);

                String input = String.format("telephone=%s&latitude=%s&longitude=%s", tel, lat, lon);

                OutputStream out = new BufferedOutputStream(client.getOutputStream());
                out.write(input.getBytes());
                out.flush();
                out.close();

            } catch (IOException e) {
                Log.d("HTTPUpdate", e.getMessage());
            }

            try {
                BufferedReader in = new BufferedReader(new InputStreamReader(client.getInputStream()));
                String line = in.readLine();

                if (line.startsWith("{1")) {
                    String[] coord = line.substring(1, line.indexOf('}')).split(",");

                    double latitude = Double.valueOf(coord[1]);
                    double longitude = Double.valueOf(coord[2]);
                    location = new LatLng(latitude, longitude);
                } else {
                    location = new LatLng(latitude, longitude);
                }

            } catch (IOException e) {
                Log.d("HTTPUpdate", e.getMessage());
            }

            return booleans[0];
        }

        @Override
        protected void onPostExecute(Boolean result) {

        }
    }


}
