import Alpine from 'alpinejs';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createIcons, House, LayoutGrid, CalendarDays, BriefcaseBusiness, CircleHelp, Sprout, ArrowUpRight, ArrowRight, ArrowLeft, ArrowDownRight, LogOut, Menu, MapPin, Sun, Search, Shapes, Sparkles, Droplets, Zap, PaintRoller, AirVent, Star, UserRound, HousePlus, CalendarCheck2, MessageCircleHeart, ListChecks, CircleCheck, CircleAlert, SearchX, Info, Clock3, ShieldCheck, CalendarCheck, HandCoins, Plus, Wallet, ChartNoAxesCombined, ClipboardCheck, Armchair, ChevronRight, CalendarPlus, MessageSquare, Check, Phone, HandHeart, Pencil, Trash2, LockKeyhole, UsersRound, Handshake, X } from 'lucide';
const icons = {House, LayoutGrid, CalendarDays, BriefcaseBusiness, CircleHelp, Sprout, ArrowUpRight, ArrowRight, ArrowLeft, ArrowDownRight, LogOut, Menu, MapPin, Sun, Search, Shapes, Sparkles, Droplets, Zap, PaintRoller, AirVent, Star, UserRound, HousePlus, CalendarCheck2, MessageCircleHeart, ListChecks, CircleCheck, CircleAlert, SearchX, Info, Clock3, ShieldCheck, CalendarCheck, HandCoins, Plus, Wallet, ChartNoAxesCombined, ClipboardCheck, Armchair, ChevronRight, CalendarPlus, MessageSquare, Check, Phone, HandHeart, Pencil, Trash2, LockKeyhole, UsersRound, Handshake, X};
import '@fontsource/dm-sans/latin-400.css';
import '@fontsource/dm-sans/latin-500.css';
import '@fontsource/dm-sans/latin-600.css';
import '@fontsource/dm-sans/latin-700.css';
import '@fontsource/manrope/latin-500.css';
import '@fontsource/manrope/latin-600.css';
import '@fontsource/manrope/latin-700.css';
import '@fontsource/manrope/latin-800.css';

window.Alpine = Alpine;
Alpine.data('bookingForm', (url, initialDate, initialTime) => ({
    date: initialDate, time: initialTime, unavailable: [], loading: false, slotError: '',
    async loadSlots() {
        this.loading = true; this.slotError = ''; const requestedDate = this.date;
        try {
            const response = await fetch(url + '?date=' + encodeURIComponent(requestedDate), {headers:{Accept:'application/json'}});
            if (!response.ok) throw new Error('Could not check availability. Choose a valid date and try again.');
            const result = await response.json();
            if (this.date !== requestedDate) return;
            this.unavailable = result.unavailable;
            if (this.unavailable.includes(this.time)) this.time = '';
        } catch (error) { if (this.date === requestedDate) this.slotError = error.message; }
        finally { if (this.date === requestedDate) this.loading = false; }
    }
}));
Alpine.start();
createIcons({icons});
gsap.registerPlugin(ScrollTrigger);
const motion = gsap.matchMedia();
motion.add('(prefers-reduced-motion: no-preference)', () => {
    document.querySelector('.hero-copy') && gsap.from('.hero-copy > *', {y:22,opacity:0,duration:0.65,stagger:0.1,ease:'power3.out'});
    document.querySelector('.hero-image') && gsap.from('.hero-image', {scale:1.06,opacity:0,duration:1.1,ease:'power2.out'});
    document.querySelectorAll('[data-stagger]').forEach(container => {
        gsap.from(container.children, {y:22,opacity:0,duration:0.55,stagger:0.07,ease:'power2.out',scrollTrigger:{trigger:container,start:'top 95%',once:true}});
    });
    document.querySelector('.page-heading') && gsap.from('.page-heading', {y:12,opacity:0,duration:0.5});
});
document.querySelectorAll('form[data-submit]').forEach(form => {
    form.addEventListener('submit', () => {
        if (!form.checkValidity()) return;
        const button = form.querySelector('button[type="submit"]');
        if (button) {button.disabled = true;button.dataset.originalText = button.innerHTML;button.textContent = 'Please wait…';}
    });
});
window.addEventListener('pageshow', () => document.querySelectorAll('button[data-original-text]').forEach(button => {button.disabled=false;button.innerHTML=button.dataset.originalText;createIcons({icons});}));
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', e => {if (!window.confirm(form.dataset.confirm)) e.preventDefault();});
});
