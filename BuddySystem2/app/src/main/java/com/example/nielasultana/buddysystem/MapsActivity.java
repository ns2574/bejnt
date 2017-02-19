package com.example.nielasultana.buddysystem;

import android.Manifest;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.AsyncTask;
import android.os.Handler;
import android.support.v4.app.ActivityCompat;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Timer;
import java.util.TimerTask;

public class MapsActivity extends AppCompatActivity implements OnMapReadyCallback {

    private static final int PERMISSION_REQUEST_CODE = 1;
    private static final int LOCATION_REQUEST_CODE = 2;
    private static final int UPDATE_INTERVAL = 5000;
    private static final String URL_UPDATE = "http://52.37.120.183/hacknyu17/update.php";

    private GoogleMap map;
    private String telephone;
    private Button report;
    private Marker marker;
    private Timer timer;
    private double longitude;
    private double latitude;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);

        ActionBar actionBar = getSupportActionBar();
        if (actionBar != null) {
            actionBar.setDisplayHomeAsUpEnabled(true);
            actionBar.setTitle("Helper Location");
        }

        telephone = getIntent().getStringExtra("id");

        SupportMapFragment mapFragment = (SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map);
        mapFragment.getMapAsync(this);

        report = (Button) findViewById(R.id.reportOptions);
        report.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showReportOptions();
            }
        });

        updateTimer();
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
                        new GetUpdate(telephone, String.valueOf(latitude), String.valueOf(longitude)).execute(false);
                    }
                });
            }
        }, 0, UPDATE_INTERVAL);
    }

    @Override
    public void onMapReady(GoogleMap googleMap) {
        map = googleMap;

        marker = map.addMarker(new MarkerOptions().position(new LatLng(0, 0)));
        getLocation();
        new GetUpdate(telephone, String.valueOf(latitude), String.valueOf(longitude)).execute(true);

        getPermission();
    }

    private void updateMarker(LatLng location, boolean setCamera) {
        marker.setPosition(location);
        if (setCamera) {
            map.moveCamera(CameraUpdateFactory.newLatLngZoom(location, 15f));
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {
        if (requestCode == PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getPermission();
            }
        }

        if (requestCode == LOCATION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getLocation();
            }
        }
    }

    private void getPermission() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_DENIED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, PERMISSION_REQUEST_CODE);
        } else {
            map.setMyLocationEnabled(true);
        }
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

    private void showReportOptions() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        View view = getLayoutInflater().inflate(R.layout.report_popup, null);

        Button first = (Button) view.findViewById(R.id.first);
        Button second = (Button) view.findViewById(R.id.second);
        Button third = (Button) view.findViewById(R.id.third);

        builder.setView(view);
        final AlertDialog ad = builder.create();

        first.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
                Toast.makeText(MapsActivity.this, "Thank you for your report!", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });

        second.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
                Toast.makeText(MapsActivity.this, "The caller will be flagged", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });

        third.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
                Toast.makeText(MapsActivity.this, "The authorities have been contacted", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });


        second.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
                Toast.makeText(MapsActivity.this, "The caller will be flagged", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });


        third.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
                Toast.makeText(MapsActivity.this, "Authorities have been contacted", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });


        ad.show();
    }

    @Override
    public void onBackPressed() {
        timer.cancel();
        Intent intent = new Intent(MapsActivity.this, MainActivity.class);
        startActivity(intent);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == android.R.id.home) {
            onBackPressed();
        }

        return false;
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
            updateMarker(location, result);
        }
    }
}


