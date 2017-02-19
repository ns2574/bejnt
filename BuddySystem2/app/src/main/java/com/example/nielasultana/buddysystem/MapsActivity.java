package com.example.nielasultana.buddysystem;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
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

import java.util.Timer;
import java.util.TimerTask;

public class MapsActivity extends AppCompatActivity implements OnMapReadyCallback {

    private static final int PERMISSION_REQUEST_CODE = 1;
    private static final int UPDATE_INTERVAL = 5000;

    private GoogleMap map;
    private Button report;
    private Marker marker;
    private String telephone;
    private Timer timer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);

        ActionBar actionBar = getSupportActionBar();
        if(actionBar != null) {
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
                        new GetUpdate().execute(false);
                    }
                });
            }
        }, 0, UPDATE_INTERVAL);
    }

    @Override
    public void onMapReady(GoogleMap googleMap) {
        map = googleMap;

        marker = map.addMarker(new MarkerOptions().position(new LatLng(0, 0)));
        new GetUpdate().execute(true);

        getPermission();
    }

    private void updateMarker(LatLng location, boolean setCamera){
        marker.setPosition(location);
        if(setCamera) {
            map.moveCamera(CameraUpdateFactory.newLatLngZoom(location, 15f));
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {
        if(requestCode == PERMISSION_REQUEST_CODE){
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getPermission();
            }
        }
    }

    private void getPermission(){
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_DENIED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, PERMISSION_REQUEST_CODE);
        } else {
            map.setMyLocationEnabled(true);
        }
    }

    private void showReportOptions(){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        View view = getLayoutInflater().inflate(R.layout.report_popup, null);

        Button first = (Button) view.findViewById(R.id.first);

        builder.setView(view);
        final AlertDialog ad = builder.create();

        first.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Toast.makeText(MapsActivity.this, "No event occurred", Toast.LENGTH_SHORT).show();
                ad.cancel();
            }
        });

        ad.show();
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent(MapsActivity.this, MainActivity.class);
        startActivity(intent);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if(item.getItemId() == android.R.id.home){
            onBackPressed();
        }

        return false;
    }

    private class GetUpdate extends AsyncTask<Boolean, Void, Boolean> {

        private LatLng location;

        @Override
        protected Boolean doInBackground(Boolean... booleens) {
            //TODO: Get updated location

            double longitude = 0;
            double latitude = 0;
            location = new LatLng(latitude, longitude);

            return booleens[0];
        }

        @Override
        protected void onPostExecute(Boolean result) {
            updateMarker(location, result);
        }
    }

}
