How to update your bundle ?
===========================

This document explains how to upgrade from one FOSUSerBundle version to
the next one. It only discusses changes that need to be done when using
the "public" API of the bundle. If you "hack" the core, you should probably
follow the timeline closely anyway.

* The form classes have been moved to subnamespaces to keep them organized.

* The ACL implementation using JMSSecurityExtraBundle which was broken
  since Symfony beta2 has been removed.

* The Twig block has been renamed from `content` to `fos_user_content`.
