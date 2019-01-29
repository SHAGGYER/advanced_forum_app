import Install from './pages/Install';
import CategoriesShow from './pages/CategoriesShow';
import CategoriesCreate from './pages/CategoriesCreate';
import ProfileShow from './pages/ProfileShow';
import ForumCreate from './pages/ForumCreate';
import ForumShow from './pages/ForumShow';
import ThreadCreate from './pages/ThreadCreate';
import ThreadShow from './pages/ThreadShow';
import Search from './pages/Search';
import Notifications from './pages/Notifications';

const routes = [
    {
        path: '/install',
        component: Install,
        name: 'install'
    },

    {
        path: '/',
        component: CategoriesShow,
        name: 'categories.show'
    },

    {
        path: '/search',
        component: Search,
        name: 'search'
    },

    {
        path: '/categories/create',
        component: CategoriesCreate,
        name: 'categories.create'
    },
    {
        path: '/forum/:id',
        component: ForumShow,
        name: 'forum.show',
    },

    {
        path: '/forum/create',
        component: ForumCreate,
        name: 'forum.create'
    },

    {
        path: '/thread/create',
        component: ThreadCreate,
        name: 'thread.create'
    },

    {
        path: '/thread/:id',
        component: ThreadShow,
        name: 'thread.show'
    },

    {
        path: '/profile',
        component: ProfileShow,
        name: 'profile.show'
    },

    {
        path: '/notifications',
        component: Notifications,
        name: 'notifications'
    }



];

export default routes;