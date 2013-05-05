--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- Name: check_rating; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_rating AS ENUM (
    'hidden',
    'info',
    'low_risk',
    'med_risk',
    'high_risk'
);


ALTER TYPE public.check_rating OWNER TO postgres;

--
-- Name: check_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_status AS ENUM (
    'open',
    'in_progress',
    'stop',
    'finished'
);


ALTER TYPE public.check_status OWNER TO postgres;

--
-- Name: project_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE project_status AS ENUM (
    'open',
    'in_progress',
    'finished'
);


ALTER TYPE public.project_status OWNER TO gtta;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE user_role AS ENUM (
    'admin',
    'user',
    'client'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- Name: vuln_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE vuln_status AS ENUM (
    'open',
    'resolved'
);


ALTER TYPE public.vuln_status OWNER TO gtta;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- Name: check_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_categories_id_seq OWNER TO gtta;

--
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 11, true);


--
-- Name: check_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories_l10n (
    check_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_categories_l10n OWNER TO gtta;

--
-- Name: check_controls; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls (
    id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_controls OWNER TO gtta;

--
-- Name: check_controls_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_controls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_controls_id_seq OWNER TO gtta;

--
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- Name: check_controls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_controls_id_seq', 17, true);


--
-- Name: check_controls_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls_l10n (
    check_control_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_controls_l10n OWNER TO gtta;

--
-- Name: check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    description character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    value character varying,
    type integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_inputs OWNER TO gtta;

--
-- Name: check_inputs_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_inputs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_inputs_id_seq OWNER TO gtta;

--
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 47, true);


--
-- Name: check_inputs_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs_l10n (
    check_input_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    description character varying
);


ALTER TABLE public.check_inputs_l10n OWNER TO gtta;

--
-- Name: check_results; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_results OWNER TO gtta;

--
-- Name: check_results_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_results_id_seq OWNER TO gtta;

--
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 5, true);


--
-- Name: check_results_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results_l10n (
    check_result_id bigint NOT NULL,
    language_id bigint NOT NULL,
    result character varying,
    title character varying(1000)
);


ALTER TABLE public.check_results_l10n OWNER TO gtta;

--
-- Name: check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    solution character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_solutions OWNER TO gtta;

--
-- Name: check_solutions_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_solutions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_solutions_id_seq OWNER TO gtta;

--
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 7, true);


--
-- Name: check_solutions_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions_l10n (
    check_solution_id bigint NOT NULL,
    language_id bigint NOT NULL,
    solution character varying,
    title character varying(1000)
);


ALTER TABLE public.check_solutions_l10n OWNER TO gtta;

--
-- Name: checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks (
    id bigint NOT NULL,
    check_control_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    background_info character varying,
    hints character varying,
    advanced boolean NOT NULL,
    automated boolean NOT NULL,
    script character varying(1000),
    multiple_solutions boolean NOT NULL,
    protocol character varying(1000),
    port integer,
    question character varying,
    reference_id bigint NOT NULL,
    reference_code character varying(1000),
    reference_url character varying(1000),
    effort integer DEFAULT 0 NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- Name: checks_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.checks_id_seq OWNER TO gtta;

--
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 50, true);


--
-- Name: checks_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks_l10n (
    check_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    background_info character varying,
    hints character varying,
    reference character varying,
    question character varying
);


ALTER TABLE public.checks_l10n OWNER TO gtta;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE clients (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    country character varying(1000),
    state character varying(1000),
    city character varying(1000),
    address character varying(1000),
    postcode character varying(1000),
    website character varying(1000),
    contact_name character varying(1000),
    contact_phone character varying(1000),
    contact_email character varying(1000),
    contact_fax character varying(1000),
    logo_path character varying(1000),
    logo_type character varying(1000)
);


ALTER TABLE public.clients OWNER TO gtta;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO gtta;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 4, true);


--
-- Name: emails; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE emails (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying NOT NULL,
    attempts integer DEFAULT 0 NOT NULL,
    sent boolean DEFAULT false NOT NULL
);


ALTER TABLE public.emails OWNER TO gtta;

--
-- Name: emails_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.emails_id_seq OWNER TO gtta;

--
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- Name: emails_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('emails_id_seq', 17, true);


--
-- Name: languages; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE languages (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    code character(2) NOT NULL,
    "default" boolean NOT NULL
);


ALTER TABLE public.languages OWNER TO gtta;

--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.languages_id_seq OWNER TO gtta;

--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('languages_id_seq', 3, true);


--
-- Name: login_history; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE login_history (
    id bigint NOT NULL,
    user_id bigint,
    user_name character varying(1000) NOT NULL,
    create_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.login_history OWNER TO gtta;

--
-- Name: login_history_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE login_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.login_history_id_seq OWNER TO gtta;

--
-- Name: login_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE login_history_id_seq OWNED BY login_history.id;


--
-- Name: login_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('login_history_id_seq', 48, true);


--
-- Name: project_details; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_details (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying
);


ALTER TABLE public.project_details OWNER TO gtta;

--
-- Name: project_details_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE project_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.project_details_id_seq OWNER TO gtta;

--
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 4, true);


--
-- Name: project_users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_users (
    project_id bigint NOT NULL,
    user_id bigint NOT NULL,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_users OWNER TO gtta;

--
-- Name: projects; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    year character(4) NOT NULL,
    deadline date NOT NULL,
    name character varying(1000) NOT NULL,
    status project_status DEFAULT 'open'::project_status NOT NULL,
    vuln_overdue date
);


ALTER TABLE public.projects OWNER TO gtta;

--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO gtta;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 13, true);


--
-- Name: references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE "references" (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    url character varying(1000)
);


ALTER TABLE public."references" OWNER TO gtta;

--
-- Name: references_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.references_id_seq OWNER TO gtta;

--
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- Name: references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('references_id_seq', 2, true);


--
-- Name: report_template_sections; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections (
    id bigint NOT NULL,
    report_template_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    intro character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections OWNER TO gtta;

--
-- Name: report_template_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_template_sections_id_seq OWNER TO gtta;

--
-- Name: report_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_sections_id_seq OWNED BY report_template_sections.id;


--
-- Name: report_template_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_sections_id_seq', 4, true);


--
-- Name: report_template_sections_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections_l10n (
    report_template_section_id bigint NOT NULL,
    language_id bigint NOT NULL,
    intro character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections_l10n OWNER TO gtta;

--
-- Name: report_template_summary; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary (
    id bigint NOT NULL,
    summary character varying,
    rating_from numeric(3,2) DEFAULT 0.00 NOT NULL,
    rating_to numeric(3,2) DEFAULT 0.00 NOT NULL,
    report_template_id bigint NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary OWNER TO gtta;

--
-- Name: report_template_summary_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_summary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_template_summary_id_seq OWNER TO gtta;

--
-- Name: report_template_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_summary_id_seq OWNED BY report_template_summary.id;


--
-- Name: report_template_summary_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_summary_id_seq', 4, true);


--
-- Name: report_template_summary_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary_l10n (
    report_template_summary_id bigint NOT NULL,
    language_id bigint NOT NULL,
    summary character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary_l10n OWNER TO gtta;

--
-- Name: report_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates (
    id bigint NOT NULL,
    name character varying(1000),
    header_image_path character varying(1000),
    header_image_type character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying
);


ALTER TABLE public.report_templates OWNER TO gtta;

--
-- Name: report_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_templates_id_seq OWNER TO gtta;

--
-- Name: report_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_templates_id_seq OWNED BY report_templates.id;


--
-- Name: report_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_templates_id_seq', 3, true);


--
-- Name: report_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates_l10n (
    report_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying
);


ALTER TABLE public.report_templates_l10n OWNER TO gtta;

--
-- Name: risk_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories (
    id bigint NOT NULL,
    name character varying(1000),
    risk_template_id bigint NOT NULL
);


ALTER TABLE public.risk_categories OWNER TO gtta;

--
-- Name: risk_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.risk_categories_id_seq OWNER TO gtta;

--
-- Name: risk_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_categories_id_seq OWNED BY risk_categories.id;


--
-- Name: risk_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_categories_id_seq', 20, true);


--
-- Name: risk_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories_l10n (
    risk_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_categories_l10n OWNER TO gtta;

--
-- Name: risk_category_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_category_checks (
    risk_category_id bigint NOT NULL,
    check_id bigint NOT NULL,
    damage integer DEFAULT 0 NOT NULL,
    likelihood integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.risk_category_checks OWNER TO gtta;

--
-- Name: risk_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates (
    id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates OWNER TO gtta;

--
-- Name: risk_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.risk_templates_id_seq OWNER TO gtta;

--
-- Name: risk_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_templates_id_seq OWNED BY risk_templates.id;


--
-- Name: risk_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_templates_id_seq', 6, true);


--
-- Name: risk_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates_l10n (
    risk_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates_l10n OWNER TO gtta;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE sessions (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.sessions OWNER TO gtta;

--
-- Name: system; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE system (
    id bigint NOT NULL,
    backup timestamp without time zone
);


ALTER TABLE public.system OWNER TO gtta;

--
-- Name: system_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE system_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.system_id_seq OWNER TO gtta;

--
-- Name: system_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE system_id_seq OWNED BY system.id;


--
-- Name: system_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('system_id_seq', 2, true);


--
-- Name: target_check_attachments; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_attachments (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    type character varying(1000) NOT NULL,
    path character varying(1000) NOT NULL,
    size bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_attachments OWNER TO gtta;

--
-- Name: target_check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_categories (
    target_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    advanced boolean NOT NULL,
    check_count bigint DEFAULT 0 NOT NULL,
    finished_count bigint DEFAULT 0 NOT NULL,
    low_risk_count bigint DEFAULT 0 NOT NULL,
    med_risk_count bigint DEFAULT 0 NOT NULL,
    high_risk_count bigint DEFAULT 0 NOT NULL,
    info_count bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_categories OWNER TO gtta;

--
-- Name: target_check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_inputs (
    target_id bigint NOT NULL,
    check_input_id bigint NOT NULL,
    value character varying,
    file character varying(1000),
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_inputs OWNER TO gtta;

--
-- Name: target_check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_solutions (
    target_id bigint NOT NULL,
    check_solution_id bigint NOT NULL,
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_solutions OWNER TO gtta;

--
-- Name: target_check_vulns; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_vulns (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    user_id bigint,
    deadline date,
    status vuln_status DEFAULT 'open'::vuln_status NOT NULL
);


ALTER TABLE public.target_check_vulns OWNER TO gtta;

--
-- Name: target_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_checks (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying,
    target_file character varying(1000),
    rating check_rating,
    started timestamp without time zone,
    pid bigint,
    status check_status DEFAULT 'open'::check_status NOT NULL,
    result_file character varying(1000),
    language_id bigint NOT NULL,
    protocol character varying(1000),
    port integer,
    override_target character varying(1000),
    user_id bigint NOT NULL,
    table_result character varying
);


ALTER TABLE public.target_checks OWNER TO gtta;

--
-- Name: target_references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_references (
    target_id bigint NOT NULL,
    reference_id bigint NOT NULL
);


ALTER TABLE public.target_references OWNER TO gtta;

--
-- Name: targets; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE targets (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    host character varying(1000) NOT NULL,
    description character varying(1000)
);


ALTER TABLE public.targets OWNER TO gtta;

--
-- Name: targets_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.targets_id_seq OWNER TO gtta;

--
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 6, true);


--
-- Name: users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    email character varying(1000) NOT NULL,
    password character varying(1000) NOT NULL,
    name character varying(1000),
    client_id bigint,
    role user_role DEFAULT 'admin'::user_role NOT NULL,
    last_action_time timestamp without time zone,
    send_notifications boolean DEFAULT false NOT NULL,
    password_reset_code character varying(1000),
    password_reset_time timestamp(6) without time zone
);


ALTER TABLE public.users OWNER TO gtta;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO gtta;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 4, true);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history ALTER COLUMN id SET DEFAULT nextval('login_history_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections ALTER COLUMN id SET DEFAULT nextval('report_template_sections_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary ALTER COLUMN id SET DEFAULT nextval('report_template_summary_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates ALTER COLUMN id SET DEFAULT nextval('report_templates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories ALTER COLUMN id SET DEFAULT nextval('risk_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates ALTER COLUMN id SET DEFAULT nextval('risk_templates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY system ALTER COLUMN id SET DEFAULT nextval('system_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
2	FTP
3	SMTP
4	SSH
5	TCP
6	Web Anonymous
1	DNS
8	Eine Kleine
10	zed
9	AUTHENTICATED WEB CHECKS
11	New & Modified Checks
\.


--
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories_l10n (check_category_id, language_id, name) FROM stdin;
2	1	FTP
2	2	\N
3	1	SMTP
3	2	\N
4	1	SSH
4	2	\N
5	1	TCP
5	2	\N
6	1	Web Anonymous
6	2	\N
1	1	DNS
1	2	\N
8	1	\N
8	2	Eine Kleine
10	1	zed
10	2	\N
9	1	AUTHENTICATED WEB CHECKS
9	2	Piuyyy
11	1	New & Modified Checks
11	2	\N
\.


--
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name, sort_order) FROM stdin;
2	2	Default	2
3	3	Default	3
4	4	Default	4
5	5	Default	5
6	6	Default	6
10	9	SESSION HANDLING & COOKIES	10
12	11	New checks	12
14	1	2	14
15	1	3	15
16	1	4	16
17	1	5	17
11	1	Empty Control	9
13	1	1	11
8	1	Session Handling	13
1	1	Default	1
7	1	This is a long name of the control	7
9	1	Some other important stuff	8
\.


--
-- Data for Name: check_controls_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls_l10n (check_control_id, language_id, name) FROM stdin;
2	1	Default
2	2	\N
3	1	Default
3	2	\N
4	1	Default
4	2	\N
5	1	Default
5	2	\N
6	1	Default
6	2	\N
7	1	This is a long name of the control
7	2	\N
9	1	Some other important stuff
9	2	\N
11	1	Empty Control
11	2	\N
8	1	Session Handling
8	2	\N
10	1	SESSION HANDLING & COOKIES
10	2	\N
1	1	Default
1	2	zzz
12	1	New checks
12	2	\N
13	1	1
13	2	\N
14	1	2
14	2	\N
15	1	3
15	2	\N
16	1	4
16	2	\N
17	1	5
17	2	\N
\.


--
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value, type) FROM stdin;
5	5	Timeout		0	120	0
6	5	Debug		1	1	0
7	6	Show All		0	0	0
8	8	Timeout		0	10	0
9	8	Max Results		1	100	0
10	8	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	2	1	0
11	12	Hostname		0		0
12	13	Hostname		0		0
13	15	Long List		0	0	0
14	16	Long List		0	0	0
15	17	Users		0		0
16	17	Passwords		1		0
17	20	Recipient		0		0
18	20	Server		1		0
19	20	Login		2		0
20	20	Password		3		0
21	20	Sender		4		0
22	20	Folder		5		0
23	21	Timeout		0	10	0
24	21	Source E-mail		1	source@gmail.com	0
25	21	Destination E-mail		2	destination@gmail.com	0
26	22	Users		0		0
27	22	Passwords		1		0
28	23	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	0		0
29	23	Timeout	Timeout in milliseconds.	1	1000	0
30	24	Port Range	2 lines: start and end of the range.	0	1\r\n80	0
31	26	Range Count		0	10	0
32	32	Code	Possible values: php, cfm, asp.	0	php	0
34	35	Paths		0		0
33	34	URLs		0		0
35	36	Paths		0		0
36	37	Paths		0		0
37	42	Timeout		0	10	0
38	43	Page Type	Possible values: php, asp.	0	php	0
39	43	Cookies		1		0
40	43	URL Limit		2	100	0
42	45	Hostname		0		0
1	1	Hostname		0	asdfasdf	0
45	1	Here Goes Select	Multiple Values	3	kinda\r\nmultiple\r\nvalues\r\nto\r\nselect	0
44	1	Checkbox	Is good enough	2	hellooo	0
43	1	Text Field		1	Some Default Value	0
46	41	Admin Logins		0		4
47	41	Adobe xml		1		4
\.


--
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description) FROM stdin;
5	1	Timeout	\N
5	2	\N	\N
6	1	Debug	\N
6	2	\N	\N
7	1	Show All	\N
7	2	\N	\N
8	1	Timeout	\N
8	2	\N	\N
9	1	Max Results	\N
9	2	\N	\N
10	1	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check
10	2	\N	\N
11	1	Hostname	\N
11	2	\N	\N
12	1	Hostname	\N
12	2	\N	\N
13	1	Long List	\N
13	2	\N	\N
14	1	Long List	\N
14	2	\N	\N
15	1	Users	\N
15	2	\N	\N
16	1	Passwords	\N
16	2	\N	\N
17	1	Recipient	\N
17	2	\N	\N
18	1	Server	\N
18	2	\N	\N
19	1	Login	\N
19	2	\N	\N
20	1	Password	\N
20	2	\N	\N
21	1	Sender	\N
21	2	\N	\N
22	1	Folder	\N
22	2	\N	\N
23	1	Timeout	\N
23	2	\N	\N
24	1	Source E-mail	\N
24	2	\N	\N
25	1	Destination E-mail	\N
25	2	\N	\N
26	1	Users	\N
26	2	\N	\N
27	1	Passwords	\N
27	2	\N	\N
28	1	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)
28	2	\N	\N
29	1	Timeout	Timeout in milliseconds.
29	2	\N	\N
30	1	Port Range	2 lines: start and end of the range.
30	2	\N	\N
31	1	Range Count	\N
31	2	\N	\N
32	1	Code	Possible values: php, cfm, asp.
32	2	\N	\N
34	1	Paths	\N
34	2	\N	\N
33	1	URLs	\N
33	2	\N	\N
35	1	Paths	\N
35	2	\N	\N
36	1	Paths	\N
36	2	\N	\N
37	1	Timeout	\N
37	2	\N	\N
38	1	Page Type	Possible values: php, asp.
38	2	\N	\N
39	1	Cookie	\N
39	2	\N	\N
40	1	URL Limit	\N
40	2	\N	\N
42	1	Hostname	\N
42	2	\N	\N
1	1	Hostname	\N
1	2	\N	\N
45	1	Here Goes Select	Multiple Values
45	2	\N	\N
44	1	Checkbox	Is good enough
44	2	\N	\N
43	1	Text Field	\N
43	2	\N	\N
46	1	Admin Logins	\N
46	2	\N	\N
47	1	Adobe xml	\N
47	2	\N	\N
\.


--
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order, title) FROM stdin;
3	3	Resulten	1	Test Deutsche
2	3	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	0	Test English
5	46	zzz & xxx <a> lolo	0	xxx
\.


--
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result, title) FROM stdin;
3	1	\N	\N
3	2	Resulten	Test Deutsche
2	1	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	Test English
2	2	Result ' Pizda Dzhigurda (de)	Zuzuz
5	1	zzz & xxx <a> lolo	xxx
5	2	\N	\N
\.


--
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order, title) FROM stdin;
5	3	i love you too	1	tears of prophecy
4	3	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	0	aduljadei
7	46	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br><br><ol><li>asdfsadf</li><li>asdfasdf</li></ol><br>sdfsadf<br><ul><li>dsf</li><li>asdfasdf</li></ul><br>	0	Fuck something
\.


--
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution, title) FROM stdin;
5	1	i love you too	tears of prophecy
5	2	ich liebe dir	\N
4	1	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	aduljadei
4	2	\N	\N
7	1	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br><br><ol><li>asdfsadf</li><li>asdfasdf</li></ol><br>sdfsadf<br><ul><li>dsf</li><li>asdfasdf</li></ul><br>	Fuck something
7	2	\N	\N
\.


--
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_control_id, name, background_info, hints, advanced, automated, script, multiple_solutions, protocol, port, question, reference_id, reference_code, reference_url, effort, sort_order) FROM stdin;
17	2	FTP Bruteforce			f	t	ftp_bruteforce.pl	f		\N		1			2	17
18	3	SMTP Banner			f	t	smtp_banner.py	f		\N		1			2	18
19	3	SMTP DNSBL			f	t	smtp_dnsbl.py	f		\N		1			2	19
20	3	SMTP Filter			f	t	smtp_filter.py	f		\N		1			2	20
21	3	SMTP Relay			f	t	smtp_relay.pl	f		\N		1			2	21
13	1	DNS SPF			f	t	dns_spf.py	f		\N		1			2	16
12	1	DNS SOA			f	t	dns_soa.py	f		\N		1			2	15
10	1	DNS NS Version			f	t	ns_version.pl	f		\N		1			2	13
16	1	DNS Top TLDs			f	t	dns_top_tlds.pl	f		\N		1			2	45
9	1	DNS NIC Whois			f	t	nic_whois.pl	f		\N		1			2	12
7	1	DNS IP Range			f	t	dns_ip_range.pl	f		\N		1			2	10
15	1	DNS Subdomain Bruteforce			f	t	subdomain_bruteforce.pl	f		\N		1			2	6
22	4	SSH Bruteforce			f	t	ssh_bruteforce.pl	f		\N		1			2	22
23	5	Nmap Port Scan			f	t	pscan.pl	f		\N		1			2	23
24	5	TCP Port Scan			f	t	portscan.pl	f		\N		1			2	24
25	5	TCP Traceroute			f	t	tcp_traceroute.py	f		80		1			2	25
26	6	Apache DoS			f	t	apache_dos.pl	f		\N		1			2	26
27	6	Fuzz Check			f	t	fuzz_check.pl	f		\N		1			2	27
28	6	Google URL			f	t	google_url.pl	f		\N		1			2	28
29	6	Grep URL			f	t	grep_url.pl	f	http	\N		1			2	29
30	6	HTTP Banner			f	t	http_banner.pl	f	http	\N		1			2	30
31	6	Joomla Scan			f	t	joomla_scan.pl	f	http	\N		1			2	31
32	6	Login Pages			f	t	login_pages.pl	f	http	\N		1			2	32
33	6	Nikto			f	t	nikto.pl	f	http	80		1			2	33
34	6	URL Scan			f	t	urlscan.pl	f	http	\N		1			2	34
35	6	Web Auth Scanner			f	t	www_auth_scanner.pl	f	http	80		1			2	35
36	6	Web Directory Scanner			f	t	www_dir_scanner.pl	f	http	80		1			2	36
37	6	Web File Scanner			f	t	www_file_scanner.pl	f	http	80		1			2	37
38	6	Web HTTP Methods			f	t	web_http_methods.py	f		\N		1			2	38
39	6	Web Server CMS			f	t	webserver_cms.pl	f		\N		1			2	39
40	6	Web Server Error Message			f	t	webserver_error_msg.pl	f		\N		1			2	40
41	6	Web Server Files			f	t	webserver_files.pl	f		\N		1			2	41
42	6	Web Server SSL			f	t	webserver_ssl.pl	f		\N		1			2	42
43	6	Web SQL XSS			f	t	web_sql_xss.py	f		\N		1			2	43
5	7	DNS Find NS			f	t	dns_find_ns.pl	f		\N		1			2	5
8	8	DNS NIC Typosquatting			f	t	nic_typosquatting.pl	f		\N		1			2	8
11	8	DNS Resolve IP			f	t	dns_resolve_ip.pl	f		\N		1			2	11
14	9	DNS SPF (Perl)			f	t	dns_spf.pl	f		\N		1			2	14
47	12	CMS check			f	t	cms_detection.py	f	http	80		1			2	47
48	10	yay			f	f		f		\N		1			2	48
49	13	hh			f	f		f		\N		1			2	49
3	1	DNS AFXR	hey <b>fuck \\' sss</b><br><b>How are you?<br></b>sd<br><b></b>1. this is some kind of list<br>2. lololo upup up<br>sdfa<br>asdf<br>asdf<br>sdd<br>sdf<br>sdf	jjj<br>what the fuck did you do?	f	t	dns_afxr.pl	f		\N	No more no more	1			2	7
50	1	DNS TEST	test		f	f		f		\N		1			2	50
45	1	DNS A (Non-Recursive)			f	t	dns_a_nr.py	f		\N		1			2	3
1	1	DNS A	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.		f	t	dns_a.py	f		\N		1			2	0
6	1	DNS Hosting	hello		f	t	dns_hosting.py	f		\N		1			2	9
46	10	Scan Somethingh	<b>Background Info:</b><br>The http-server headers often leak information about servertype and versions. In our check we request the server via http 1.0 with and without host-header to check for misconfigured vhosts and via a usual http 1.1-request. Having the servertype and version an attacker can start investigating if there are known exploits for that specific version. This helps the attacker to prepare all targeted attacks. We will look specifically for the following tags:<br><br><ul><li>Server: A name for the server including sometimes the version.</li><li>Via: Informs the client of proxies through which the response was sent.</li><li>X-Powered-By: specifies the technology (e.g. ASP.NET, PHP, JBoss) supporting the web application (version details are often in X-Runtime, X-Version, or X-AspNet-Version)</li></ul>	<span>HELO MYDOMAIN<br></span><ol><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ol><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br></span><ul><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ul><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br></span></span><ul><li>\r\nInfoguard AG</li></ul>	f	t	test.py	t		\N	<span>HELO MYDOMAIN<br></span><ul><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ul><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br></span><ol><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ol><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>	1			2	46
\.


--
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, hints, reference, question) FROM stdin;
7	1	DNS IP Range	\N	\N	\N	\N
7	2	\N	\N	\N	\N	\N
9	1	DNS NIC Whois	\N	\N	\N	\N
9	2	\N	\N	\N	\N	\N
10	1	DNS NS Version	\N	\N	\N	\N
10	2	\N	\N	\N	\N	\N
12	1	DNS SOA	\N	\N	\N	\N
12	2	\N	\N	\N	\N	\N
13	1	DNS SPF	\N	\N	\N	\N
13	2	\N	\N	\N	\N	\N
15	1	DNS Subdomain Bruteforce	\N	\N	\N	\N
15	2	\N	\N	\N	\N	\N
16	1	DNS Top TLDs	\N	\N	\N	\N
16	2	\N	\N	\N	\N	\N
17	1	FTP Bruteforce	\N	\N	\N	\N
17	2	\N	\N	\N	\N	\N
18	1	SMTP Banner	\N	\N	\N	\N
18	2	\N	\N	\N	\N	\N
19	1	SMTP DNSBL	\N	\N	\N	\N
19	2	\N	\N	\N	\N	\N
20	1	SMTP Filter	\N	\N	\N	\N
20	2	\N	\N	\N	\N	\N
21	1	SMTP Relay	\N	\N	\N	\N
21	2	\N	\N	\N	\N	\N
22	1	SSH Bruteforce	\N	\N	\N	\N
22	2	\N	\N	\N	\N	\N
23	1	Nmap Port Scan	\N	\N	\N	\N
23	2	\N	\N	\N	\N	\N
24	1	TCP Port Scan	\N	\N	\N	\N
24	2	\N	\N	\N	\N	\N
25	1	TCP Traceroute	\N	\N	\N	\N
25	2	\N	\N	\N	\N	\N
26	1	Apache DoS	\N	\N	\N	\N
26	2	\N	\N	\N	\N	\N
27	1	Fuzz Check	\N	\N	\N	\N
27	2	\N	\N	\N	\N	\N
28	1	Google URL	\N	\N	\N	\N
28	2	\N	\N	\N	\N	\N
29	1	Grep URL	\N	\N	\N	\N
29	2	\N	\N	\N	\N	\N
30	1	HTTP Banner	\N	\N	\N	\N
30	2	\N	\N	\N	\N	\N
31	1	Joomla Scan	\N	\N	\N	\N
31	2	\N	\N	\N	\N	\N
32	1	Login Pages	\N	\N	\N	\N
32	2	\N	\N	\N	\N	\N
33	1	Nikto	\N	\N	\N	\N
33	2	\N	\N	\N	\N	\N
34	1	URL Scan	\N	\N	\N	\N
34	2	\N	\N	\N	\N	\N
35	1	Web Auth Scanner	\N	\N	\N	\N
35	2	\N	\N	\N	\N	\N
36	1	Web Directory Scanner	\N	\N	\N	\N
36	2	\N	\N	\N	\N	\N
37	1	Web File Scanner	\N	\N	\N	\N
37	2	\N	\N	\N	\N	\N
38	1	Web HTTP Methods	\N	\N	\N	\N
38	2	\N	\N	\N	\N	\N
39	1	Web Server CMS	\N	\N	\N	\N
39	2	\N	\N	\N	\N	\N
40	1	Web Server Error Message	\N	\N	\N	\N
40	2	\N	\N	\N	\N	\N
41	1	Web Server Files	\N	\N	\N	\N
41	2	\N	\N	\N	\N	\N
42	1	Web Server SSL	\N	\N	\N	\N
42	2	\N	\N	\N	\N	\N
43	1	Web SQL XSS	\N	\N	\N	\N
43	2	\N	\N	\N	\N	\N
5	1	DNS Find NS	\N	\N	\N	\N
5	2	\N	\N	\N	\N	\N
8	1	DNS NIC Typosquatting	\N	\N	\N	\N
8	2	\N	\N	\N	\N	\N
11	1	DNS Resolve IP	\N	\N	\N	\N
11	2	\N	\N	\N	\N	\N
14	1	DNS SPF (Perl)	\N	\N	\N	\N
14	2	\N	\N	\N	\N	\N
45	1	DNS A (Non-Recursive)	\N	\N	\N	\N
45	2	\N	\N	\N	\N	\N
6	1	DNS Hosting	hello	\N	\N	\N
6	2	\N	\N	\N	\N	\N
3	1	DNS AFXR	hey <b>fuck \\' sss</b><br><b>How are you?<br></b>sd<br><b></b>1. this is some kind of list<br>2. lololo upup up<br>sdfa<br>asdf<br>asdf<br>sdd<br>sdf<br>sdf	jjj<br>what the fuck did you do?	\N	No more no more
3	2	ZXZXZXX	meowsfvfd<br>sdfsdf<br>sdf<br>sdf<br>sd<br>f<br>sdf<br>sd<br>f<br>sd<br>f<br>sdf	piu<i> poiu</i>	\N	\N
1	1	DNS A	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
1	2	ZZZ	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
46	2	\N	\N	\N	\N	\N
50	1	DNS TEST	test	\N	\N	\N
47	1	CMS check	\N	\N	\N	\N
50	2	\N	\N	\N	\N	\N
47	2	\N	\N	\N	\N	\N
48	1	yay	\N	\N	\N	\N
48	2	\N	\N	\N	\N	\N
49	1	hh	\N	\N	\N	\N
49	2	\N	\N	\N	\N	\N
46	1	Scan Somethingh	<b>Background Info:</b><br>The http-server headers often leak information about servertype and versions. In our check we request the server via http 1.0 with and without host-header to check for misconfigured vhosts and via a usual http 1.1-request. Having the servertype and version an attacker can start investigating if there are known exploits for that specific version. This helps the attacker to prepare all targeted attacks. We will look specifically for the following tags:<br><br><ul><li>Server: A name for the server including sometimes the version.</li><li>Via: Informs the client of proxies through which the response was sent.</li><li>X-Powered-By: specifies the technology (e.g. ASP.NET, PHP, JBoss) supporting the web application (version details are often in X-Runtime, X-Version, or X-AspNet-Version)</li></ul>	<span>HELO MYDOMAIN<br></span><ol><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ol><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br></span><ul><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ul><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br></span></span><ul><li>\r\nInfoguard AG</li></ul>	\N	<span>HELO MYDOMAIN<br></span><ul><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ul><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br></span><ol><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ol><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email, contact_fax, logo_path, logo_type) FROM stdin;
2	Ziga										\N	\N	\N
4	Helloy										123-123-123	\N	\N
1	Test	Switzerland		Zurich	Kallison Lane, 7	123456	http://netprotect.ch	Ivan John		invan@john.com	123-123-123	40453852965cebb2b0dbc5440323bea3f5adf750c8b5f72e06b5fc7a9aad9da4	image/png
\.


--
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
1	English	en	t
2	Deutsch	de	f
\.


--
-- Data for Name: login_history; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY login_history (id, user_id, user_name, create_time) FROM stdin;
1	1	Oliver Muenchow	2013-01-20 19:00:34.601315
2	1	Oliver Muenchow	2013-01-20 19:00:46.822096
3	1	Oliver Muenchow	2013-01-21 00:36:16.786873
4	1	Oliver Muenchow	2013-01-21 00:36:48.513369
5	1	Oliver Muenchow	2013-01-21 01:39:25.282192
7	1	Oliver Muenchow	2013-01-21 01:41:35.632161
6	\N	test@client.com	2013-01-21 01:41:18.967625
8	1	Oliver Muenchow	2013-01-21 12:45:25.103189
9	1	Oliver Muenchow	2013-01-21 18:55:41.217336
10	1	Oliver Muenchow	2013-01-22 00:09:47.837418
11	1	Oliver Muenchow	2013-01-22 12:48:55.146417
12	1	Oliver Muenchow	2013-01-22 14:48:23.294127
13	1	Oliver Muenchow	2013-01-22 19:29:43.01782
14	1	Oliver Muenchow	2013-01-23 17:36:02.634146
15	1	Oliver Muenchow	2013-01-24 06:05:48.900624
16	1	Oliver Muenchow	2013-01-24 19:51:32.786624
17	1	Oliver Muenchow	2013-02-07 16:25:53.558536
18	1	Oliver Muenchow	2013-02-08 16:11:20.123214
19	1	Oliver Muenchow	2013-02-08 19:36:41.043217
20	1	Oliver Muenchow	2013-02-09 02:51:28.691028
21	1	Oliver Muenchow	2013-02-09 02:59:42.890879
22	1	Oliver Muenchow	2013-02-09 04:40:16.891014
23	1	Oliver Muenchow	2013-02-09 07:15:45.180509
24	1	Oliver Muenchow	2013-02-18 04:03:35.828318
25	1	Oliver Muenchow	2013-02-18 13:23:46.080472
26	1	Oliver Muenchow	2013-02-20 17:36:22.531282
27	1	Oliver Muenchow	2013-03-07 20:46:32.692731
28	1	Oliver Muenchow	2013-04-17 12:34:46.254237
29	1	Oliver Muenchow	2013-05-04 16:21:28.136106
30	3	erbol@gmail.com	2013-05-04 16:21:42.171008
31	1	Oliver Muenchow	2013-05-04 16:22:04.624138
32	3	erbol@gmail.com	2013-05-04 16:29:49.91426
33	1	Oliver Muenchow	2013-05-04 16:30:00.750086
34	3	erbol@gmail.com	2013-05-04 16:30:27.665802
35	1	Oliver Muenchow	2013-05-04 16:31:02.818463
36	3	erbol@gmail.com	2013-05-04 16:32:27.164168
37	1	Oliver Muenchow	2013-05-04 16:58:10.953036
38	3	erbol@gmail.com	2013-05-04 17:47:53.349979
39	1	Oliver Muenchow	2013-05-04 18:02:57.663391
40	4	Anton Belousov	2013-05-04 18:22:40.866149
41	1	Oliver Muenchow	2013-05-04 18:24:03.418928
42	1	Oliver Muenchow	2013-05-05 00:00:47.46133
43	1	Oliver Muenchow	2013-05-05 01:41:26.172397
44	1	Oliver Muenchow	2013-05-05 02:32:43.503971
45	1	Oliver Muenchow	2013-05-05 02:39:20.934522
46	1	Oliver Muenchow	2013-05-05 02:43:44.088959
47	1	Oliver Muenchow	2013-05-05 13:02:39.753248
48	1	Oliver Muenchow	2013-05-05 15:14:46.241212
\.


--
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
2	1	hello	world
3	2	kekek	kkk\r\n
4	1	yaya	aazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzz
\.


--
-- Data for Name: project_users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_users (project_id, user_id, admin) FROM stdin;
13	1	t
1	3	f
2	3	f
13	4	f
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status, vuln_overdue) FROM stdin;
6	1	2012	2012-09-21	aaa	in_progress	\N
3	1	2012	2012-09-21	xxx	open	\N
4	2	2012	2012-09-21	yyy	open	\N
8	2	2012	2012-09-21	ccc	open	\N
9	2	2012	2012-09-21	fff	open	\N
10	1	2012	2012-09-21	ddd	open	\N
11	2	2012	2012-09-21	eee	open	\N
12	2	2013	2012-09-21	kokokoko	open	\N
5	1	2012	2012-09-21	zzz	finished	\N
2	2	2012	2012-07-29	Fuck	finished	\N
1	2	2012	2012-07-27	Test	in_progress	2012-09-28
13	4	2012	2013-02-09	Buka	open	\N
\.


--
-- Data for Name: references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY "references" (id, name, url) FROM stdin;
1	CUSTOM	
\.


--
-- Data for Name: report_template_sections; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections (id, report_template_id, check_category_id, intro, sort_order, title) FROM stdin;
4	1	1	key key	1	Key
1	1	9	Project admin:&nbsp;{admin} hey<br><br>OLOLO<br><ul><li>все вы быдло</li><li>и хуйло</li></ul><br>	0	Hey
\.


--
-- Data for Name: report_template_sections_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections_l10n (report_template_section_id, language_id, intro, title) FROM stdin;
4	1	key key	Key
4	2	\N	\N
1	1	Project admin:&nbsp;{admin} hey<br><br>OLOLO<br><ul><li>все вы быдло</li><li>и хуйло</li></ul><br>	Hey
1	2	Project admin:&nbsp;{admin}	kkkkk
\.


--
-- Data for Name: report_template_summary; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary (id, summary, rating_from, rating_to, report_template_id, title) FROM stdin;
4		1.00	2.00	1	Hello
3	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;<br><br><ul><li>list</li><li>goes here</li></ul><br>and here is a numbered list<br><ol><li>one</li><li>two</li></ol>yay<br>	0.00	5.00	1	Everything is fine!
\.


--
-- Data for Name: report_template_summary_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary_l10n (report_template_summary_id, language_id, summary, title) FROM stdin;
4	1	\N	Hello
4	2	\N	\N
3	1	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;<br><br><ul><li>list</li><li>goes here</li></ul><br>and here is a numbered list<br><ol><li>one</li><li>two</li></ol>yay<br>	Everything is fine!
3	2	\N	\N
\.


--
-- Data for Name: report_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates (id, name, header_image_path, header_image_type, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro) FROM stdin;
3	Yay ;)	\N	\N					\N	\N	\N	\N	\N	\N	\N	\N
1	Test Template	0caf7534e0fee7a603c2948652ab8de6815ccea0b277340d7122269f4a847c89	image/png	Test Template Intro<br><ul><li>The client is: {client}</li><li>The project is: {project}</li><li>Project year:&nbsp;<b>{year}</b></li></ul>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br><ol><li>Date from: {date.from}</li><li>Date to: {date.to}</li></ol>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}<br>This text goes after the list of targets.<br><b>well done<br><br>S-tats<br>{target.stats}<br><br>And here is a list of targets with controls lol:<br></b>{target.weakest}<br><b><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b><b>Here go top 5 vulns:<br></b>{vuln.list}<b><br></b>well done	Test Template Appendix<br><ol><li>one</li><li>two</li><li>three</li></ol>	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}<br><br><ul><li>hello</li><li>wtf is that</li></ul>No way<br><ol><li>one list</li><li>two lists</li></ol>	test one two&nbsp;{targets}<br><br>vuln distribution<br><br>with<br><ul><li>some</li><li>list</li></ul>	reduced targets {targets}	high risk&nbsp;targets {targets}	med risk&nbsp;targets {targets}	low risk&nbsp;targets {targets}	degree&nbsp;targets {targets}<br><br><ol><li>degree</li><li>list</li></ol>	risk&nbsp;targets {targets}<br><br>risk<br><ul><li>matrix</li><li>list</li></ul>
\.


--
-- Data for Name: report_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates_l10n (report_template_id, language_id, name, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro) FROM stdin;
3	1	Yay ;)	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
1	2	zzz	Testen Templaten Intro	Testen Templaten Appendix	Worlda	\N	test eins zwei&nbsp;{targets}	test eins zwei&nbsp;{targets}	\N	\N	\N	\N	\N	\N
1	1	Test Template	Test Template Intro<br><ul><li>The client is: {client}</li><li>The project is: {project}</li><li>Project year:&nbsp;<b>{year}</b></li></ul>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br><ol><li>Date from: {date.from}</li><li>Date to: {date.to}</li></ol>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}<br>This text goes after the list of targets.<br><b>well done<br><br>S-tats<br>{target.stats}<br><br>And here is a list of targets with controls lol:<br></b>{target.weakest}<br><b><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b><b>Here go top 5 vulns:<br></b>{vuln.list}<b><br></b>well done	Test Template Appendix<br><ol><li>one</li><li>two</li><li>three</li></ol>	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}<br><br><ul><li>hello</li><li>wtf is that</li></ul>No way<br><ol><li>one list</li><li>two lists</li></ol>	test one two&nbsp;{targets}<br><br>vuln distribution<br><br>with<br><ul><li>some</li><li>list</li></ul>	reduced targets {targets}	high risk&nbsp;targets {targets}	med risk&nbsp;targets {targets}	low risk&nbsp;targets {targets}	degree&nbsp;targets {targets}<br><br><ol><li>degree</li><li>list</li></ol>	risk&nbsp;targets {targets}<br><br>risk<br><ul><li>matrix</li><li>list</li></ul>
\.


--
-- Data for Name: risk_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories (id, name, risk_template_id) FROM stdin;
20	Pipa	6
\.


--
-- Data for Name: risk_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories_l10n (risk_category_id, language_id, name) FROM stdin;
20	1	Pipa
20	2	\N
\.


--
-- Data for Name: risk_category_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_category_checks (risk_category_id, check_id, damage, likelihood) FROM stdin;
20	46	1	1
20	48	1	1
20	49	1	1
20	1	1	1
20	3	1	1
20	45	1	1
20	6	1	1
20	7	1	1
20	9	1	1
20	10	1	1
20	12	1	1
20	13	1	1
20	15	1	1
20	16	1	1
20	8	1	1
20	11	1	1
20	14	1	1
20	5	1	1
20	17	1	1
20	47	1	1
20	18	1	1
20	19	1	1
20	20	1	1
20	21	1	1
20	22	1	1
20	23	1	1
20	24	1	1
20	25	1	1
20	26	1	1
20	27	1	1
20	28	1	1
20	29	1	1
20	30	1	1
20	31	1	1
20	32	1	1
20	33	1	1
20	34	1	1
20	35	1	1
20	36	1	1
20	37	1	1
20	38	1	1
20	39	1	1
20	40	1	1
20	41	1	1
20	42	1	1
20	43	1	1
\.


--
-- Data for Name: risk_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates (id, name) FROM stdin;
6	test
\.


--
-- Data for Name: risk_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates_l10n (risk_template_id, language_id, name) FROM stdin;
6	1	test
6	2	\N
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY sessions (id, expire, data) FROM stdin;
k66ggbpoi9batdih8ktk4g7la4      	1367759710	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
gfo39ingvm1amsg1kat90e04s3      	1367757731	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
64nss2d1evfdaaqrobpkrute41      	1367759482	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
1llk0tjmgaas4r8p936pd402i2      	1367759721	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
arslpeu023e7m1dujcgp8an0e2      	1367759619	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
aeluterhpsrmol913mbr6fd6f1      	1367759737	NQ1qsPftHzwYq8FCTnCTcIu6PvwKSml3-8jiOKgk_jioRFpW71XGMx9bvj5-TYBmbn73BS3gu-kWgzMkHckejbDaxEE9E9JypzXUyWK_nk2qN5ePnoO2r4-ORKSxG8sIeNpbPulYxGi8mvpafWC4d1ioxJPWy-HC6pmi_Coi4yr-2_ch3xEvSA4TtXK9KWpiS0MTwVQNa4atrGnAu804LCEv38zXMG3dZnKV0yiO5cl0PJE10y0IidjKJV36oKA2sJtCyudx9WFWZ3OEw3gV8MfaCEsCP1JJ4V4W89opSym_6PzMKURLKDsWIeOTwglEpVqiimnXJqJC0HRDdac31g..
c2gm3p2o2mic98fi8sfc932nk1      	1367759529	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
ltilit4oh0a7fuiarq3cgi0n00      	1367758558	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
m4jsugpkkoq2m50ljej4ho38s0      	1367759483	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
gqmblg11rjrdsrmdj2ptt0qaf5      	1367759532	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
o1lj077urp3kaq4u6jc8k7usm0      	1367759629	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
nk80e6if8a90ebi56oflf415e2      	1367759712	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
cbhdqk9g4rq98so2kqbn74uci0      	1367759727	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
g4kam3uq9u693p2jlk9nr25ta4      	1367758558	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
rom0dduatu0g78517fbo0e2ir0      	1367759488	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
gllar90pib5620esfr1d4nbek6      	1367759532	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
c2nbg9k8jf3el5jtpg3b3p8i86      	1367759651	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
o5s15p8jsedhadf5fsd782mmr2      	1367759713	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
k7f8q29majeap7f9iojomdatg5      	1367759737	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
d45lfssi9i27802dqjr6ih7iq5      	1367757731	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
6o008emuli0bt6tjmrttngpd31      	1367758566	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
q30kau23bs84b23t03r6kk0i96      	1367759493	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
h73mqeb9ufqv3hd7rs36nkp091      	1367759597	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
bn7asvo7vgmo9gqn201b6qm4f6      	1367759658	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
90fjr9lj7aiut691ap7rog06o4      	1367759718	xVmPKMuobDurd2x7OpPqvKcoeMd7uzENavZu4y_5hksOt_Ya_F3DwBbcTfrpNEpcrKtt1TQSDiqG2o0lf2VDq16dKPMy4BR7tnZQf4vkynOwnV0cWtGYRwOjt4GxY0dc
\.


--
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY system (id, backup) FROM stdin;
1	2012-11-21 17:55:00.535688
\.


--
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
2	27	_eng_images_support_down_photo_m_ek (1).jpg	image/jpeg	fd3c6970b8053745020efceb15883a50147e657969a961540ab08ce18e564b7d	272561
\.


--
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count, info_count) FROM stdin;
1	9	t	2	1	0	0	0	1
1	6	t	18	2	0	0	0	0
2	3	t	4	0	0	0	0	0
1	8	t	0	0	0	0	0	0
1	3	t	4	0	0	0	0	0
1	4	t	1	0	0	0	0	0
1	5	t	3	0	0	0	0	0
1	10	t	0	0	0	0	0	0
6	6	t	18	3	0	1	0	2
1	2	t	1	1	0	0	0	1
1	11	t	1	1	0	0	0	0
2	6	t	18	5	0	2	0	2
1	1	t	17	15	0	3	0	0
4	1	t	17	0	0	0	0	0
5	1	t	17	0	0	0	0	0
\.


--
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
1	1	asdfasdf	\N	1
1	43	Some Default Value	\N	1
1	44	1	\N	1
1	5	120	\N	5
1	6	1	\N	5
1	15	\N	\N	17
1	16	\N	\N	17
1	45	multiple	\N	1
1	7	0	\N	6
2	31	10	\N	26
2	32	php	\N	32
1	42	google.com	\N	45
1	46	1	ca9b973efccbd438b561c0bbe293d5d8aa65bac36d2462c8d9112dccdc7175f1	41
1	47	0	fa650a3dca3d2e54caaf729e55f2a7a278db85450b48c212cd011b17f4123c3c	41
1	12	\N	\N	13
1	11	\N	ab5cc45403bbbf854133a57dd176be8a79aea45e9b9107d45eb634ac763ea322	12
1	8	10	a93b7ccbb92e169a839b12760bd3ea2df83e9fb57f69629d887962f0136672fc	8
1	9	10	c3aaa0222672bd78ce935654e50172da8374d6a2366d4896cd6e111375b3480a	8
1	10	1	f6d144ffb1ee26991b62293e5c059cc598e5e2c00fdbfc15fee0831e989c8385	8
1	14	0	e2d4e99aedc6467c533ceb9c476dfb64b1767bdc8f86f8ed4cba103d488b4cec	16
1	13	0	77cceff0199091fdf1e9e51e7a0c66602a5e9379db0ac1f19efa623362c983cd	15
\.


--
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- Data for Name: target_check_vulns; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_vulns (target_id, check_id, user_id, deadline, status) FROM stdin;
\.


--
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id, table_result) FROM stdin;
1	5	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	17	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	6	\N	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	14	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	11	query failed: NXDOMAIN	ba958ad7b51889ad6b8cfc6c06d9d334417e50010bafe54f2d3df41eb8bd7524	\N	2012-10-12 03:19:08.402282	\N	finished	7b0b9efbc239fb0be3e01c8c7409a51e49797f209f914961c7002a585541ddeb	1	\N	\N	fuck it all	1	\N
2	26	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	28	\N	\N	hidden	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1	\N
6	28	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
6	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1	\N
2	27	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
6	30	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1	\N
1	8		b83d3c16886e3985ce1eb4c828ba1251d6488a13bb4d7ecc3be6670273200229	\N	2013-01-24 06:08:38.396468	\N	finished	bba14063fbb51e9345682ebe334b1a4c42d85f8be96ae847dcaee0f0f246cecd	1	\N	\N	google.com	1	<gtta-table><columns><column width="0.5" name="Domain"/><column width="0.5" name="IP"/></columns><row><cell>gosgle.com</cell><cell>54.248.125.234</cell></row><row><cell>googpe.com</cell><cell>69.6.27.100</cell></row><row><cell>googlz.com</cell><cell>204.13.160.107</cell></row><row><cell>koogle.com</cell><cell>208.87.34.15</cell></row><row><cell>koogle.com</cell><cell>74.86.197.160</cell></row><row><cell>grogle.com</cell><cell>141.8.224.106</cell></row><row><cell>goole.com</cell><cell>213.165.70.39</cell></row></gtta-table>
1	13	No output.	5f60a27e9c2111670b7ac522d67ea4a28ea0f82830f54082b96b699dfe045aa4	\N	\N	\N	finished	3bff8cdab5a34d561ea20db0b51d64c820448d8d00dac24f0fab2fbe657acbe8	1	\N	\N	\N	1	\N
1	47	No output.	daff9c357f092f45dd347a9ceb199488924a8148820d568eaede13a94728f2a1	\N	2012-11-26 07:09:01.609125	\N	finished	f08845086cba4dc36b7eaccd7071742540942bef7383300185ccb93211904cc6	1	http	80	infoguard.com	1	\N
1	48	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	32	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1	\N
1	7	Internal server error. Please send this error code to the administrator - A783BAE2332FE839.	8db8e350872d7f8f5a904757929873d40b4907527b806750727e9acb5fb0ae31	\N	\N	\N	finished	9efe9da38c259364f97fa07233a34a5bb43ae7a83ff2777ec8d021c8b72a40e5	1	\N	\N	80.248.198.9 - 80.248.198.14	1	\N
1	9	Internal server error. Please send this error code to the administrator - 4500C4EA45249D2B.	2a29974663f07789e79bb70d5eeb12a174d8c3618de10bc50182f977e2f5c82b	\N	\N	\N	finished	6f1536eb2f83fd2dd9ca4c1a00424ce2af483b1ab4fbcc632125fae9a4128e7d	1	\N	\N	80.248.198.9	1	\N
1	12	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\nns3.google.com                 IP 216.239.36.10    SOA 2013015000  2h         30m        14d        5m        \nns4.google.com                 IP 216.239.38.10    SOA 2013015000  2h         30m        14d        5m        \nns1.google.com                 IP 216.239.32.10    SOA 2013015000  2h         30m        14d        5m        \nns2.google.com                 IP 216.239.34.10    SOA 2013015000  2h         30m        14d        5m        \nThe recommended syntax for serial number is YYYYMMDDnn (YYYY=year, MM=month, DD=day, nn=revision number), RFC 1912 (2013015000)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (5m)\n	ef581d8726c59821f7e4f88bbe8cd78de01f23d24dfb1b132cfd4fce87c2f05e	\N	2013-01-21 02:01:22.216199	\N	finished	86a074c1f106e46b6a33cc55934da18d190ad034fa93254db0bd63593e705f8a	1	\N	\N	\N	1	\N
1	16		39e7de7a2aad466f032a8a2f65d894c712d9f1b66651983c9969e44d3d26fa63	\N	2013-01-24 06:11:06.556805	\N	finished	82ebe7c26d634ad9f23b5ae5ecb12a4f8f80c178e633601916c2b51735860bb2	1	\N	\N	netprotect.ch	1	<gtta-table><columns><column width="0.3" name="Domain"/><column width="0.2" name="IP"/><column width="0.2" name="Whois"/><column width="0.3" name="Title"/></columns><row><cell>netprotect.com</cell><cell>54.243.62.158</cell><cell>NETPROTECT.COM</cell><cell>Unblock Us - smarter faster VPN</cell></row><row><cell>netprotect.info</cell><cell>82.98.86.173</cell><cell>na</cell><cell>netprotect.info - ÑÑÐ¾ Ð½Ð°Ð¸Ð»ÑÑÑÐ¸Ð¹ Ð¸ÑÑÐ¾ÑÐ½Ð¸Ðº Ð¸Ð½ÑÐ¾ÑÐ¼Ð°ÑÐ¸Ð¸ Ð¿Ð¾ ÑÐµÐ¼Ðµ netprotect. Ð­ÑÐ¾Ñ Ð²ÐµÐ±-ÑÐ°Ð¹Ñ Ð¿ÑÐ¾Ð´Ð°ÐµÑÑÑ</cell></row><row><cell>netprotect.net</cell><cell>208.91.197.54</cell><cell>NETPROTECT.NET</cell><cell>Loading...</cell></row><row><cell>netprotect.org</cell><cell>64.95.64.218</cell><cell>Buydomains.com</cell><cell>N/A</cell></row><row><cell>netprotect.ws</cell><cell>64.70.19.198</cell><cell>N/A</cell><cell>Find what you are looking for...</cell></row></gtta-table>
1	1	kkkk	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	<gtta-table>\n    <columns>\n        <column name="Value" width="0.3"/>\n        <column name="Name" width="0.3"/>\n        <column name="Data" width="0.4"/>\n    </columns>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n</gtta-table>
1	45	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	46	\n	896996d050a9d84c568b8d6aa3020f72abfc9a86b3ec556c38cd351ba25e96dc	\N	2013-05-05 16:15:30.903981	\N	finished	ee172921f8d4365a452b17800aa7d70f78571be4b65f585bbf3620f0ffea2916	1	\N	\N	netprotect.ch	1	\N
1	15		cf3067eb8f2c87ca32d5dcb8f99d51a3bd1241c63b4d60ea9d273e66d8412f1d	\N	2013-01-24 06:15:02.631314	\N	finished	aa3754015e2174188254e999ebde44af5ad7304677cb66d8f4231e0b90394206	1	\N	\N	netprotect.ch	1	<gtta-table><columns><column width="0.5" name="Domain"/><column width="0.5" name="IP"/></columns><row><cell>dev.netprotect.ch</cell><cell>81.6.58.118</cell></row><row><cell>Wildcard DNS: 15 hostname(s)</cell><cell>213.239.210.108</cell></row></gtta-table>
1	10	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	3	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	27	tried 879 time(s) with 0 successful time(s)\n	d1ed11b5e9259aa95e347e6cfb242c6f29264193deff6dca98e7fd06e1ec0ca4	\N	2013-01-21 01:52:55.779798	\N	finished	30a05cb7ef48b856ec5256e9ee09e294c7a9fe0776f94879bc71ee50c2a2be63	1	\N	\N	onexchanger.com	1	\N
1	41	**** running Interesting files and dirs scanner ****\n+ interesting Files And Dirs takes awhile....\n+ interesting File or Dir Found: "/logon.php\r"\n+ Item "/logon.php\r" contains header: "Content-Length: 0" MAYBE a False Positive or is empty!\n+ interesting File or Dir Found: "/backend.php\r"\n+ Item "/backend.php\r" contains header: "Content-Length: 0" MAYBE a False Positive or is empty!\n+ interesting File or Dir Found: "/"\n	6b645ef5542a30fab667de4727ebeee69906af3bac2de527b3689cbd1e24d4dd	\N	2013-02-09 07:20:37.218053	\N	finished	a4664d7394c7d9a5948b7189a0172e319ab95c0e36892980421848a3fa8dcf10	1	\N	\N	demonstratr.com	1	\N
\.


--
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
1	1
2	1
3	1
4	1
5	1
6	1
\.


--
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host, description) FROM stdin;
3	1	empty.com	\N
4	2	127.0.0.1	\N
5	12	127.0.0.1	\N
2	1	test.com	\N
6	6	test.com	\N
1	1	google.com	Main Webserver
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role, last_action_time, send_notifications, password_reset_code, password_reset_time) FROM stdin;
3	erbol@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		2	client	2013-05-04 18:02:54.270777	f	abd9aeef114d88f28ac0ad83fecb70c25a7e22500872eab947ade90244889ee9	\N
4	bob@bob.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Anton Belousov	\N	user	2013-05-04 18:23:59.902642	f	\N	\N
1	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Oliver Muenchow	\N	admin	2013-05-05 16:15:37.727447	t	\N	2013-05-05 02:41:23.449866
\.


--
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: login_history_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_pkey PRIMARY KEY (id);


--
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- Name: project_users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_pkey PRIMARY KEY (project_id, user_id);


--
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- Name: report_template_sections_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_pkey PRIMARY KEY (report_template_section_id, language_id);


--
-- Name: report_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_pkey PRIMARY KEY (id);


--
-- Name: report_template_sections_report_template_id_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_key UNIQUE (report_template_id, check_category_id);


--
-- Name: report_template_summary_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_pkey PRIMARY KEY (report_template_summary_id, language_id);


--
-- Name: report_template_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_pkey PRIMARY KEY (id);


--
-- Name: report_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_pkey PRIMARY KEY (report_template_id, language_id);


--
-- Name: report_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates
    ADD CONSTRAINT report_templates_pkey PRIMARY KEY (id);


--
-- Name: risk_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_pkey PRIMARY KEY (risk_category_id, language_id);


--
-- Name: risk_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_pkey PRIMARY KEY (id);


--
-- Name: risk_category_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_pkey PRIMARY KEY (risk_category_id, check_id);


--
-- Name: risk_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_pkey PRIMARY KEY (risk_template_id, language_id);


--
-- Name: risk_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates
    ADD CONSTRAINT risk_templates_pkey PRIMARY KEY (id);


--
-- Name: sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY system
    ADD CONSTRAINT system_pkey PRIMARY KEY (id);


--
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- Name: target_check_vulns_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_pkey PRIMARY KEY (target_id, check_id);


--
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: login_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_l10n_report_template_section_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_report_template_section_id_fkey FOREIGN KEY (report_template_section_id) REFERENCES report_template_sections(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_l10n_report_template_summary_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_report_template_summary_id_fkey FOREIGN KEY (report_template_summary_id) REFERENCES report_template_summary(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_templates_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_templates_l10n_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_l10n_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_category_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_category_checks_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_templates_l10n_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_templatess_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templatess_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

