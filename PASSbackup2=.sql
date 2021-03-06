PGDMP                         u            buddysystem    9.5.2    9.5.1     �           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                       false            �           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                       false            �           1262    16561    buddysystem    DATABASE     }   CREATE DATABASE buddysystem WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';
    DROP DATABASE buddysystem;
             ex221    false                        2615    2200    public    SCHEMA        CREATE SCHEMA public;
    DROP SCHEMA public;
             ex221    false            �           0    0    SCHEMA public    COMMENT     6   COMMENT ON SCHEMA public IS 'standard public schema';
                  ex221    false    7            �           0    0    public    ACL     �   REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM ex221;
GRANT ALL ON SCHEMA public TO ex221;
GRANT ALL ON SCHEMA public TO PUBLIC;
                  ex221    false    7                        3079    13276    plpgsql 	   EXTENSION     ?   CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;
    DROP EXTENSION plpgsql;
                  false            �           0    0    EXTENSION plpgsql    COMMENT     @   COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';
                       false    1            �            1259    16595 	   emergency    TABLE     �   CREATE TABLE emergency (
    damsel bigint NOT NULL,
    "time" timestamp without time zone DEFAULT now() NOT NULL,
    damselhash character varying(256)
);
    DROP TABLE public.emergency;
       public         ex221    false    7            �            1259    16605    helpers    TABLE     �   CREATE TABLE helpers (
    damsel bigint NOT NULL,
    helper bigint NOT NULL,
    "time" timestamp without time zone NOT NULL,
    active smallint
);
    DROP TABLE public.helpers;
       public         ex221    false    7            �            1259    16630    helprequest    TABLE     �   CREATE TABLE helprequest (
    damsel bigint NOT NULL,
    helper bigint NOT NULL,
    "time" timestamp without time zone NOT NULL
);
    DROP TABLE public.helprequest;
       public         ex221    false    7            �            1259    16620    report    TABLE     �   CREATE TABLE report (
    damsel bigint NOT NULL,
    helper bigint NOT NULL,
    "time" timestamp without time zone NOT NULL,
    reason integer
);
    DROP TABLE public.report;
       public         ex221    false    7            �            1259    16562 	   user_info    TABLE     y   CREATE TABLE user_info (
    telephone bigint NOT NULL,
    latitude double precision,
    longitude double precision
);
    DROP TABLE public.user_info;
       public         ex221    false    7            �          0    16595 	   emergency 
   TABLE DATA               8   COPY emergency (damsel, "time", damselhash) FROM stdin;
    public       ex221    false    182   �        �          0    16605    helpers 
   TABLE DATA               :   COPY helpers (damsel, helper, "time", active) FROM stdin;
    public       ex221    false    183   �        �          0    16630    helprequest 
   TABLE DATA               6   COPY helprequest (damsel, helper, "time") FROM stdin;
    public       ex221    false    185   �        �          0    16620    report 
   TABLE DATA               9   COPY report (damsel, helper, "time", reason) FROM stdin;
    public       ex221    false    184   �        �          0    16562 	   user_info 
   TABLE DATA               <   COPY user_info (telephone, latitude, longitude) FROM stdin;
    public       ex221    false    181   !       f           2606    16658    emergency_damselhash_key 
   CONSTRAINT     \   ALTER TABLE ONLY emergency
    ADD CONSTRAINT emergency_damselhash_key UNIQUE (damselhash);
 L   ALTER TABLE ONLY public.emergency DROP CONSTRAINT emergency_damselhash_key;
       public         ex221    false    182    182            h           2606    16599    emergency_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY emergency
    ADD CONSTRAINT emergency_pkey PRIMARY KEY (damsel, "time");
 B   ALTER TABLE ONLY public.emergency DROP CONSTRAINT emergency_pkey;
       public         ex221    false    182    182    182            j           2606    16609    helpers_pkey 
   CONSTRAINT     _   ALTER TABLE ONLY helpers
    ADD CONSTRAINT helpers_pkey PRIMARY KEY (damsel, helper, "time");
 >   ALTER TABLE ONLY public.helpers DROP CONSTRAINT helpers_pkey;
       public         ex221    false    183    183    183    183            n           2606    16634    helprequest_pkey 
   CONSTRAINT     g   ALTER TABLE ONLY helprequest
    ADD CONSTRAINT helprequest_pkey PRIMARY KEY (damsel, helper, "time");
 F   ALTER TABLE ONLY public.helprequest DROP CONSTRAINT helprequest_pkey;
       public         ex221    false    185    185    185    185            l           2606    16624    report_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY report
    ADD CONSTRAINT report_pkey PRIMARY KEY (damsel, helper, "time");
 <   ALTER TABLE ONLY public.report DROP CONSTRAINT report_pkey;
       public         ex221    false    184    184    184    184            d           2606    16567    user_info_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY user_info
    ADD CONSTRAINT user_info_pkey PRIMARY KEY (telephone);
 B   ALTER TABLE ONLY public.user_info DROP CONSTRAINT user_info_pkey;
       public         ex221    false    181    181            o           2606    16600    emergency_damsel_fkey    FK CONSTRAINT     z   ALTER TABLE ONLY emergency
    ADD CONSTRAINT emergency_damsel_fkey FOREIGN KEY (damsel) REFERENCES user_info(telephone);
 I   ALTER TABLE ONLY public.emergency DROP CONSTRAINT emergency_damsel_fkey;
       public       ex221    false    181    182    2916            p           2606    16651    helpers_damsel_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY helpers
    ADD CONSTRAINT helpers_damsel_fkey FOREIGN KEY (damsel, helper, "time") REFERENCES helprequest(damsel, helper, "time");
 E   ALTER TABLE ONLY public.helpers DROP CONSTRAINT helpers_damsel_fkey;
       public       ex221    false    183    2926    185    185    185    183    183            r           2606    16635    helprequest_damsel_fkey    FK CONSTRAINT     ~   ALTER TABLE ONLY helprequest
    ADD CONSTRAINT helprequest_damsel_fkey FOREIGN KEY (damsel) REFERENCES user_info(telephone);
 M   ALTER TABLE ONLY public.helprequest DROP CONSTRAINT helprequest_damsel_fkey;
       public       ex221    false    2916    181    185            t           2606    16645    helprequest_damsel_fkey1    FK CONSTRAINT     �   ALTER TABLE ONLY helprequest
    ADD CONSTRAINT helprequest_damsel_fkey1 FOREIGN KEY (damsel, "time") REFERENCES emergency(damsel, "time");
 N   ALTER TABLE ONLY public.helprequest DROP CONSTRAINT helprequest_damsel_fkey1;
       public       ex221    false    2920    182    182    185    185            s           2606    16640    helprequest_helper_fkey    FK CONSTRAINT     ~   ALTER TABLE ONLY helprequest
    ADD CONSTRAINT helprequest_helper_fkey FOREIGN KEY (helper) REFERENCES user_info(telephone);
 M   ALTER TABLE ONLY public.helprequest DROP CONSTRAINT helprequest_helper_fkey;
       public       ex221    false    185    2916    181            q           2606    16625    report_damsel_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY report
    ADD CONSTRAINT report_damsel_fkey FOREIGN KEY (damsel, helper, "time") REFERENCES helpers(damsel, helper, "time");
 C   ALTER TABLE ONLY public.report DROP CONSTRAINT report_damsel_fkey;
       public       ex221    false    183    2922    183    183    184    184    184            �      x������ � �      �      x������ � �      �      x������ � �      �      x������ � �      �   ?   x�uʹ !���������bo�J%��re�УV���U!w����`ca\���|��~"��     